<?php

class MDeposit {

    /**
     * @var CI_Controller
     */
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function history(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Model('DepositModel');
        $params = $request->input_request();
        $historys = $this->CI->DepositModel->history($account_info['mobo_id'], $params['offset'], $params['limit'], $params['from'], $params['to']);
        if ($historys['code'] == 1) {
            return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $historys['detail']);
        } else {
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
        }
    }

    public function verify_sandbox(MEAPI_RequestInterface $request) {
        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);
        if ($params['type'] == 'wallet') {
            $blackbox = array(
                'code' => 1,
                'credit' => $params['money']
            );
        } else {
            $blackbox = $this->CI->MBlackbox->exchange(microtime(), $params['mobo_id'], $params['service_id'], $params['money'], $params['type'], $params['subtype']);
        }
        if ($blackbox['code'] === 1) {
            $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
            $arrPay = array(
                'mobo_id' => $params['mobo_id'],
                'money' => $params['money'],
                'credit' => $blackbox['credit'],
                'payment_type' => $params['type'],
                'info' => $params['info'],
                'service_id' => $params['service_id'],
                'channel' => '1|sandbox',
                'mobo_service_id' => $params['mobo_service_id'],
                'platform' => 'inside',
                'language' => 'vn',
                'receipt_data' => 'sandbox',
                'tracking_info' => $params['tracking_info'],
                'device_id' => $params['device_id'],
            );
            $pay = $this->CI->MoboService->pay_to_service($arrPay, TRUE);
            if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                $response = array(
                    'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                    'credit' => intval($pay['credit']),
                    'unit' => $pay['unit'],
                    'money' => $pay['money']
                );
                return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
            } else {
                MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data, $banking_info, $arrPay, $deposit_transaction)), 'BANKING_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
            }
        } else {
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('force_message' => 'Mệnh giá nạp chưa được hỗ trợ'));
        }
    }

    public function verify_banking(MEAPI_RequestInterface $request, $banking_info) {
        $this->CI->load->MEAPI_Library('BankGateway/Bank_mecorp', 'bank_gateway');
        $this->CI->load->MEAPI_Model('DepositModel');
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($banking_info['info']['language']);
        $params = $request->input_request();
        $deposit_transaction = strtoupper('db' . dechex($banking_info['mobo_id'] . rand(1111111, 9999999)));
        $tmp = $this->CI->bank_gateway->verify_transaction($banking_info['banking_transaction']);
        $transaction_info = array(
            'money' => $tmp['Amount'],
            'datetime_create' => $tmp['DateTime']
        );
        if (is_numeric($transaction_info['money']) === FALSE || intval($transaction_info['money']) < 1) {
            MEAPI_Log::writeCsv(merge_log('TRANSACTION', array($banking_info, $transaction_info, $deposit_transaction)), 'BANKING_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -3, 'transaction' => $deposit_transaction))));
        }
        $data = array(
            'mobo_id' => $banking_info['mobo_id'],
            'money' => $transaction_info['money'],
            'ip_called' => $_SERVER['REMOTE_ADDR'],
            'ip_user' => $banking_info['info']['ip'],
            'language' => $banking_info['info']['language'],
            'user_agent' => $banking_info['info']['user_agent'],
            'platform' => $banking_info['info']['platform'],
            'bank_connection_id' => $this->CI->bank_gateway->get_connection_id(),
            'bank_type' => $banking_info['bank_type'],
            'bank_code' => $banking_info['bank_code'],
            'bank_transaction' => $banking_info['banking_transaction'],
            'deposit_transaction' => $deposit_transaction,
        );

        $result = $this->CI->DepositModel->insert_deposit_banking($data);
        if (is_array($result) && $result['code'] == 1) {

            $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
            $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $banking_info['mobo_id'], $banking_info['info']['service_id'], $transaction_info['money'], 'banking');
            if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
                MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($data, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
            }
            $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
            $result_wallet = $this->CI->MWallet->deposit($banking_info['mobo_id'], $blackbox['blackbox_transaction']);
            if ($result_wallet['code'] == 1) {
                if (empty($banking_info['info']['channel']) === FALSE) {
                    $tmp_channel = explode('|', $banking_info['info']['channel']);
                    $provider = $tmp_channel[0];
                    $channel = $banking_info['info']['channel'];
                } else {
                    $provider = 0;
                    $channel = '0|error';
                }
                $data_finish = array(
                    'mobo_id' => $data['mobo_id'],
                    'money' => $data['money'],
                    'credit' => $result_wallet['detail']['credit'],
                    'type' => 'banking',
                    'blackbox_transaction' => $blackbox['blackbox_transaction'],
                    'deposit_transaction' => $data['deposit_transaction'],
                    'channel' => $channel,
                    'provider' => $provider,
                    'scope_id' => $banking_info['info']['service_id'],
                );
                $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
                if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                    if (empty($banking_info['info']['direct']) === FALSE) {
                        $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                        $direct = array(
                            'credit' => $result_wallet['detail']['credit']
                        );
                        $direct = array_merge($direct, $banking_info['info']);
                        $account_info = array(
                            'mobo_id' => $data['mobo_id']
                        );
                        $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                        $result_withdraw = $output->getArray();
                        if ($result_withdraw['code'] == 110) {
                            $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                            $arrPay = array(
                                'mobo_id' => $data['mobo_id'],
                                'money' => $data['money'],
                                'credit' => $result_wallet['detail']['credit'],
                                'payment_type' => 'bank',
                                'info' => json_encode($banking_info['info']),
                                'service_id' => $banking_info['info']['service_id'],
                                'channel' => $banking_info['info']['channel'],
                                'mobo_service_id' => $banking_info['info']['mobo_service_id'],
                                'platform' => $data['platform'],
                                'language' => $data['language'],
                                'receipt_data' => $result_withdraw['data']['receipt_data'],
                                'tracking_info' => $banking_info['info']['tracking_info'],
                                'device_id' => $banking_info['info']['device_id']
                            );
                            $pay = $this->CI->MoboService->pay_to_service($arrPay);
                            if (is_array($pay) === TRUE && intval($pay['credit']) > 0) {
                                $response = array(
                                    'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                    'credit' => intval($pay['credit']),
                                    'unit' => $pay['unit'],
                                    'money' => $pay['money']
                                );
                                return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                            } else {
                                MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data, $banking_info, $arrPay, $deposit_transaction)), 'BANKING_FAIL');
                                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
                            }
                        } else {
                            MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data, $banking_info, $direct, $deposit_transaction)), 'BANKING_FAIL');
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -8, 'transaction' => $deposit_transaction))));
                        }
                    }
                    $response = array(
                        'message' => $this->CI->Language->item('BANKING_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                        'credit' => $result_wallet['detail']['credit'],
                        'balance' => $result_wallet['detail']['balance'],
                        'money' => $data['money'],
                        'transaction' => $deposit_transaction
                    );
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                } else {
                    MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($data_finish, $banking_info, $data, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                }
            } else {
                MEAPI_Log::writeCsv(merge_log('WALLET', array($data, $banking_info, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction))));
            }
        } else {
            if ($result['detail']['error_code'] == 1062) {
                //Truong hop da goi cong tien qua IPN truoc do
                $deposit_exist = $this->CI->DepositModel->check_deposit_bank_transaction($banking_info['banking_transaction']);
                $check_exist_transaction = $this->CI->DepositModel->check_deposit_transaction($deposit_exist['deposit_transaction']);
                //echo '<pre>';
                //echo $deposit_transaction;
                //print_r($check_exist_transaction);
                if (empty($check_exist_transaction) === FALSE) {
                    $response = array(
                        'message' => $this->CI->Language->item('BANKING_SUCCESS', array('credit' => $check_exist_transaction['credit'], 'balance' => 0)),
                        'credit' => $check_exist_transaction['credit'],
                        'balance' => 0,
                        'money' => $check_exist_transaction['money'],
                        'transaction' => $deposit_exist['deposit_transaction']
                    );
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                }
                MEAPI_Log::writeCsv(merge_log('DEPOSIT_INSERT_DUPLICATE', array($data, $deposit_transaction)), 'BANKING_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_DUPLICATE', array('error_code' => -5, 'transaction' => $deposit_transaction))));
            }
            MEAPI_Log::writeCsv(merge_log('DEPOSIT_INSERT', array($data, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -4, 'transaction' => $deposit_transaction))));
        }
    }

    public function verify_card(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Library('CardGateway/Card_mecorp', 'card_gateway');
        $this->CI->load->MEAPI_Library('CardGateway/Card_8595', 'card_8595');
        $this->CI->load->MEAPI_Library('MegaCard/Card_mega', 'card_mega');
        $this->CI->load->MEAPI_Model('DepositModel');

        $params = $request->input_request();

        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);

        $this->CI->load->MEAPI_Helper('card_helper');

        if($params['telco'] != "mgc"){
            if (validate_card($params['serial'], $params['pin'], $params['telco']) === FALSE) {
                MEAPI_Log::writeCsv(merge_log('CARD_INVALID', array($params['serial'], $params['pin'], $params['telco'])), 'CARD_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_INVALID')));
            }
        }

        $deposit_transaction = strtoupper('dc' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        $data = array(
            'mobo_id' => $account_info['mobo_id'],
            'money' => 0,
            'ip_called' => $_SERVER['REMOTE_ADDR'],
            'ip_user' => ($params['ip_user'] ? $params['ip_user'] : $params['ip']),
            'language' => $params['language'],
            'user_agent' => $params['user_agent'],
            'platform' => $params['platform'],
            'card_connection_id' => $this->CI->card_gateway->get_connection_id(),
            'telco' => $params['telco'],
            'serial' => $params['serial'],
            'pin' => $params['pin'],
            'card_id' => md5($params['serial'] . $params['pin'] . $deposit_transaction),
            'deposit_transaction' => $deposit_transaction,
            'service_id' => SCOPE_ID,
            'channel' => $params['channel'],
            "access_token" => $params['access_token'],
            "game_info" => $params['info']
        );


        MEAPI_Log::writeCsv(merge_log('LOG_FIRT_INSERT_CARD', $data ), 'CARD_DEPOSIT');

        $result = $this->CI->DepositModel->insert_deposit_card($data);

        if (is_array($result) && $result['code'] == 1) {
            if ($params['vina_number']) {
                $userName = 'MEM_' . $params['vina_number'] . '_' . $account_info['mobo_id'];
            } else {
                $userName = $account_info['mobo_id'];
            }
            $data_check_card = array(
                'cardType' => $params['telco'],
                'transactionID' => $deposit_transaction,
                'orderInfo' => $deposit_transaction,
                'cardSerial' => $params['serial'],
                'cardPIN' => $params['pin'],
                'userName' => $userName,
                'language' => $params['language'],
                'service_id' => SCOPE_ID,
                'service_code' => $params['service_code']
            );

            //MegaCard
            if($data_check_card["cardType"] == "mgc"){
                $this->CI->card_mega->set_params($data_check_card);
                $result_check_card = $this->CI->card_mega->process();
                //return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => json_encode($result_check_card)));
                //status_code = 2: waiting
                //status_code : 1 : finish
                MEAPI_Log::writeCsv(merge_log('FINISH_PROCESS', array($params, $result_check_card, $deposit_transaction)), 'MEGACARD');

                if ($result_check_card['code'] !== 1 || empty($result_check_card['money']) === TRUE || is_numeric($result_check_card['money']) === FALSE) {
                    $this->CI->DepositModel->update_card_money($deposit_transaction, -1);
                    //return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_INVALID'))); //$result_check_card['msg']
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $result_check_card['msg'])); //$result_check_card['msg']
                }
            }
            else
                if (in_array($data_check_card["cardType"], array("vms", "viettel", "vina"))) {

                    $this->CI->card_8595->set_params($data_check_card);

                    $result_check_card = $this->CI->card_8595->process();

                    //status_code = 2: waiting
                    //status_code : 1 : finish
                    MEAPI_Log::writeCsv(merge_log('FINISH_PROCESS', array($params, $result_check_card, $deposit_transaction)), 'CARD8595');

                    if ($result_check_card['code'] === 1) {
                        $this->CI->DepositModel->update_card_status($deposit_transaction, array('status_code' => $result_check_card['status'], "transaction_id" => $result_check_card['transaction']));
                        $response = array(
                            'message' => $result_check_card['msg'],
                            'status_code' => $result_check_card['status'],
                            'type' => '8595',
                            'money' => $result_check_card['money'],
                            'transaction' => $deposit_transaction
                        );
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    } elseif ($result_check_card['code'] !== 1 || empty($result_check_card['money']) === TRUE || is_numeric($result_check_card['money']) === FALSE) {
                        $this->CI->DepositModel->update_card_money($deposit_transaction, -1);
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $result_check_card['msg'])); //$result_check_card['msg']
                    }
                } else {

                    $this->CI->card_gateway->set_params($data_check_card);

                    $result_check_card = $this->CI->card_gateway->process();

                    if ($result_check_card['code'] !== 1 || empty($result_check_card['money']) === TRUE || is_numeric($result_check_card['money']) === FALSE) {
                        $this->CI->DepositModel->update_card_money($deposit_transaction, -1);
                        //return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_INVALID'))); //$result_check_card['msg']
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $result_check_card['msg'])); //$result_check_card['msg']
                    }
                }


            MEAPI_Log::writeCsv(merge_log('CARD_ERQUEST_2', $params), 'CARD_FAIL');
            $result_update_card_money = $this->CI->DepositModel->update_card_money($deposit_transaction, $result_check_card['money']);
            if ($result_update_card_money == TRUE) {
                $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
                $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $account_info['mobo_id'], SCOPE_ID, $result_check_card['money'], 'card', $params['telco']);
                if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
                    MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_card, $account_info, $deposit_transaction)), 'CARD_FAIL');
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
                }

                $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
                $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);
                if ($result_wallet['code'] == 1) {
                    if (empty($params['channel']) === FALSE) {
                        $tmp_channel = explode('|', $params['channel']);
                        $provider = $tmp_channel[0];
                        $channel = $params['channel'];
                    } else {
                        $provider = 0;
                        $channel = '0|error';
                    }
                    $data_finish = array(
                        'mobo_id' => $data['mobo_id'],
                        'money' => $result_check_card['money'],
                        'credit' => $result_wallet['detail']['credit'],
                        'type' => 'card',
                        'blackbox_transaction' => $blackbox['blackbox_transaction'],
                        'deposit_transaction' => $data['deposit_transaction'],
                        'channel' => $channel,
                        'provider' => $provider,
                        'scope_id' => SCOPE_ID,
                    );
                    $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
                    if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                        if (empty($params['direct']) === FALSE) {
                            $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                            $direct = array(
                                'credit' => $result_wallet['detail']['credit']
                            );
                            $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                            $result_withdraw = $output->getArray();
                            if ($result_withdraw['code'] == 110) {
                                $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                                $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                                $arrPay = array(
                                    'mobo_id' => $account_info['mobo_id'],
                                    'money' => $result_check_card['money'],
                                    'credit' => $result_wallet['detail']['credit'],
                                    'payment_type' => 'card',
                                    'info' => $params['info'],
                                    'service_id' => SCOPE_ID,
                                    'channel' => $params['channel'],
                                    'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                                    'language' => $params['language'],
                                    'platform' => $params['platform'],
                                    'receipt_data' => $result_withdraw['data']['receipt_data'],
                                    'tracking_info' => $params['tracking_info'],
                                    'device_id' => $params['device_id'],
                                    'subtype' => $params['telco'],
                                );

                                $pay = $this->CI->MoboService->pay_to_service($arrPay);
                                if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                                    $response = array(
                                        'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                        'credit' => intval($pay['credit']),
                                        'unit' => $pay['unit'],
                                        'money' => $pay['money'],
                                        'service_data' => $pay['service_data']
                                    );
                                    return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                                } else {
                                    MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data_finish, $arrPay, $deposit_transaction)), 'CARD_FAIL');
                                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                                }
                            } else {
                                MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data_finish, $deposit_transaction, $direct)), 'CARD_FAIL');
                                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
                            }
                        }
                        $response = array(
                            'message' => $this->CI->Language->item('CARD_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                            'credit' => $result_wallet['detail']['credit'],
                            'balance' => $result_wallet['detail']['balance'],
                            'money' => $result_check_card['money'],
                            'transaction' => $deposit_transaction
                        );
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    } else {
                        MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($params, $data_finish, $deposit_transaction)), 'CARD_FAIL');
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -5, 'transaction' => $deposit_transaction))));
                    }
                } else {
                    MEAPI_Log::writeCsv(merge_log('WALLET', array($params, $account_info, $blackbox, $deposit_transaction)), 'CARD_FAIL');
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction))));
                }
            } else {
                MEAPI_Log::writeCsv(merge_log('UPDATE_CARD_MONEY', array($params, $result_check_card, $deposit_transaction)), 'CARD_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -3, 'transaction' => $deposit_transaction))));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('DEPOSIT_INSERT', array($params, $data, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -4, 'transaction' => $deposit_transaction))));
        }
    }

    public function verify_momo(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Library('Momo/MomoService', 'momo_service');
        $this->CI->load->MEAPI_Model('DepositModel');
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);
        $args = array(
            'transaction_id' => $params['transaction_id'],
            'order_id' => $params['order_id']
        );
        $this->CI->momo_service->set_params($args);
        $result_check_momo = $this->CI->momo_service->verify();

        if ($result_check_momo['status_code'] !== 0) {
            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_momo, $account_info)), 'MOMO_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $result_check_momo['message']));
        }

        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
        $deposit_transaction = strtoupper('dm' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $account_info['mobo_id'], SCOPE_ID, $result_check_momo['amount'], 'ewallet', 'momo');

        if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_momo, $account_info, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
        }
        $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
        $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);

        if ($result_wallet['code'] == 1) {
            if (empty($params['channel']) === FALSE) {
                $tmp_channel = explode('|', $params['channel']);
                $provider = $tmp_channel[0];
                $channel = $params['channel'];
            } else {
                $provider = 0;
                $channel = '0|error';
            }
            $data_finish = array(
                'mobo_id' => $account_info['mobo_id'],
                'money' => $result_check_momo['amount'],
                'credit' => $result_wallet['detail']['credit'],
                'type' => 'card',
                'blackbox_transaction' => $blackbox['blackbox_transaction'],
                'deposit_transaction' => $deposit_transaction,
                'channel' => $channel,
                'provider' => $provider,
                'scope_id' => SCOPE_ID,
            );
            $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
            if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                if (empty($params['direct']) === FALSE) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $direct = array(
                        'credit' => $result_wallet['detail']['credit']
                    );
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                    $result_withdraw = $output->getArray();
                    if ($result_withdraw['code'] == 110) {
                        $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                        $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                        $arrPay = array(
                            'mobo_id' => $account_info['mobo_id'],
                            'money' => $result_check_momo['amount'],
                            'credit' => $result_wallet['detail']['credit'],
                            'payment_type' => 'ewallet',
                            'info' => $params['info'],
                            'service_id' => SCOPE_ID,
                            'channel' => $params['channel'],
                            'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                            'language' => $params['language'],
                            'platform' => $params['platform'],
                            'receipt_data' => $result_withdraw['data']['receipt_data'],
                            'tracking_info' => $params['tracking_info'],
                            'device_id' => $params['device_id'],
                            'subtype' => 'momo',
                        );
                        $pay = $this->CI->MoboService->pay_to_service($arrPay);
                        if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                            $response = array(
                                'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                'credit' => intval($pay['credit']),
                                'unit' => $pay['unit'],
                                'money' => $pay['money'],
                                'service_data' => $pay['service_data']
                            );
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        } else {
                            MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data_finish, $arrPay, $deposit_transaction)), 'CARD_FAIL');
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                        }
                    } else {
                        MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data_finish, $deposit_transaction, $direct)), 'CARD_FAIL');
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
                    }
                }
                $response = array(
                    'message' => $this->CI->Language->item('CARD_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                    'credit' => $result_wallet['detail']['credit'],
                    'balance' => $result_wallet['detail']['balance'],
                    'money' => $result_check_momo['amount'],
                    'transaction' => $deposit_transaction
                );
                return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
            } else {
                MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($params, $data_finish, $deposit_transaction)), 'CARD_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -5, 'transaction' => $deposit_transaction))));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('WALLET', array($params, $account_info, $blackbox, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction))));
        }
    }

    public function verify_mpay(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Model('DepositModel');
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);

        $result_check_mpay = array(
            'amount' => $params['money']
        );

        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
        $deposit_transaction = strtoupper('dmp' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $account_info['mobo_id'], SCOPE_ID, $result_check_mpay['amount'], 'ewallet', 'mpay');

        if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_mpay, $account_info, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
        }
        $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
        $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);

        if ($result_wallet['code'] == 1) {
            if (empty($params['channel']) === FALSE) {
                $tmp_channel = explode('|', $params['channel']);
                $provider = $tmp_channel[0];
                $channel = $params['channel'];
            } else {
                $provider = 0;
                $channel = '0|error';
            }
            $data_finish = array(
                'mobo_id' => $account_info['mobo_id'],
                'money' => $result_check_mpay['amount'],
                'credit' => $result_wallet['detail']['credit'],
                'type' => 'card',
                'blackbox_transaction' => $blackbox['blackbox_transaction'],
                'deposit_transaction' => $deposit_transaction,
                'channel' => $channel,
                'provider' => $provider,
                'scope_id' => SCOPE_ID,
            );
            $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
            if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                if (empty($params['direct']) === FALSE) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $direct = array(
                        'credit' => $result_wallet['detail']['credit']
                    );
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                    $result_withdraw = $output->getArray();
                    if ($result_withdraw['code'] == 110) {
                        $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                        $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                        $arrPay = array(
                            'mobo_id' => $account_info['mobo_id'],
                            'money' => $result_check_mpay['amount'],
                            'credit' => $result_wallet['detail']['credit'],
                            'payment_type' => 'ewallet',
                            'info' => $params['info'],
                            'service_id' => SCOPE_ID,
                            'channel' => $params['channel'],
                            'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                            'language' => $params['language'],
                            'platform' => $params['platform'],
                            'receipt_data' => $result_withdraw['data']['receipt_data'],
                            'tracking_info' => $params['tracking_info'],
                            'device_id' => $params['device_id'],
                            'subtype' => 'mpay',
                        );
                        $pay = $this->CI->MoboService->pay_to_service($arrPay);
                        if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                            $response = array(
                                'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                'credit' => intval($pay['credit']),
                                'unit' => $pay['unit'],
                                'money' => $pay['money'],
                                'service_data' => $pay['service_data']
                            );
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        } else {
                            MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data_finish, $arrPay, $deposit_transaction)), 'CARD_FAIL');
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                        }
                    } else {
                        MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data_finish, $deposit_transaction, $direct)), 'CARD_FAIL');
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
                    }
                }
                $response = array(
                    'message' => $this->CI->Language->item('CARD_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                    'credit' => $result_wallet['detail']['credit'],
                    'balance' => $result_wallet['detail']['balance'],
                    'money' => $result_check_mpay['amount'],
                    'transaction' => $deposit_transaction
                );
                return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
            } else {
                MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($params, $data_finish, $deposit_transaction)), 'CARD_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -5, 'transaction' => $deposit_transaction))));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('WALLET', array($params, $account_info, $blackbox, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction))));
        }
    }

    public function verify_8595(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Model('DepositModel');
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);

        $result_check_8595 = array(
            'amount' => $params['money']
        );

        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
        $deposit_transaction = strtoupper('d8595' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $account_info['mobo_id'], SCOPE_ID, $result_check_8595['amount'], 'card', $params['telco']);

        if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_8595, $account_info, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
        }
        $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
        $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);

        if ($result_wallet['code'] == 1) {
            if (empty($params['channel']) === FALSE) {
                $tmp_channel = explode('|', $params['channel']);
                $provider = $tmp_channel[0];
                $channel = $params['channel'];
            } else {
                $provider = 0;
                $channel = '0|error';
            }
            $data_finish = array(
                'mobo_id' => $account_info['mobo_id'],
                'money' => $result_check_8595['amount'],
                'credit' => $result_wallet['detail']['credit'],
                'type' => 'card',
                'blackbox_transaction' => $blackbox['blackbox_transaction'],
                'deposit_transaction' => $deposit_transaction,
                'channel' => $channel,
                'provider' => $provider,
                'scope_id' => SCOPE_ID,
            );
            $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
            if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                if (empty($params['direct']) === FALSE) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $direct = array(
                        'credit' => $result_wallet['detail']['credit']
                    );
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                    $result_withdraw = $output->getArray();
                    if ($result_withdraw['code'] == 110) {
                        $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                        $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                        $arrPay = array(
                            'mobo_id' => $account_info['mobo_id'],
                            'money' => $result_check_8595['amount'],
                            'credit' => $result_wallet['detail']['credit'],
                            'payment_type' => 'card',
                            'info' => $params['info'],
                            'service_id' => SCOPE_ID,
                            'channel' => $params['channel'],
                            'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                            'language' => $params['language'],
                            'platform' => $params['platform'],
                            'receipt_data' => $result_withdraw['data']['receipt_data'],
                            'tracking_info' => $params['tracking_info'],
                            'device_id' => $params['device_id'],
                            'subtype' => $params['telco'],
                        );
                        $pay = $this->CI->MoboService->pay_to_service($arrPay);
                        if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                            $response = array(
                                'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                'credit' => intval($pay['credit']),
                                'unit' => $pay['unit'],
                                'money' => $pay['money'],
                                'service_data' => $pay['service_data']
                            );
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        } else {
                            MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data_finish, $arrPay, $deposit_transaction)), 'CARD_FAIL');
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                        }
                    } else {
                        MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data_finish, $deposit_transaction, $direct)), 'CARD_FAIL');
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
                    }
                }
                $response = array(
                    'message' => $this->CI->Language->item('CARD_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                    'credit' => $result_wallet['detail']['credit'],
                    'balance' => $result_wallet['detail']['balance'],
                    'money' => $result_check_8595['amount'],
                    'transaction' => $deposit_transaction
                );
                return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
            } else {
                MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($params, $data_finish, $deposit_transaction)), 'CARD_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -5, 'transaction' => $deposit_transaction))));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('WALLET', array($params, $account_info, $blackbox, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction))));
        }
    }

    public function verify_8595_callback(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Model('DepositModel');
        $params = $request->input_request();
        $params = array_merge($params, $account_info);
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);

        $result_check_8595 = array(
            'amount' => $params['money']
        );
        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');

        $service_id = !empty($account_info['service_id']) ? $account_info['service_id'] : SCOPE_ID;
        $deposit_transaction = !empty($account_info['transaction_id']) ? $account_info['transaction_id'] : strtoupper('d8595' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        //strtoupper('d8595' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $account_info['mobo_id'], $service_id, $result_check_8595['amount'], 'card', $params['telco']);

        if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_8595, $account_info, $deposit_transaction)), 'CARD_FAIL');
            return array("code" => 111, 'message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction)));
        }
        $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
        $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);

        if ($result_wallet['code'] == 1) {
            if (empty($account_info['channel']) === FALSE) {
                $tmp_channel = explode('|', $account_info['channel']);
                $provider = $tmp_channel[0];
                $channel = $account_info['channel'];
            } else {
                $provider = 0;
                $channel = '0|error';
            }


            $data_finish = array(
                'mobo_id' => $account_info['mobo_id'],
                'money' => $result_check_8595['amount'],
                'credit' => $result_wallet['detail']['credit'],
                'type' => 'card',
                'blackbox_transaction' => $blackbox['blackbox_transaction'],
                'deposit_transaction' => $deposit_transaction,
                'channel' => $channel,
                'provider' => $provider,
                'scope_id' => $service_id,
            );
            $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);

            if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                if (empty($account_info['direct']) === FALSE) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $direct = array(
                        'credit' => $result_wallet['detail']['credit']
                    );
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);

                    $result_withdraw = $output->getArray();

                    if ($result_withdraw['code'] == 110) {
                        $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                        $tmp_access_token = json_decode(base64_decode($account_info['access_token']), TRUE);
                        $arrPay = array(
                            'mobo_id' => $account_info['mobo_id'],
                            'money' => $result_check_8595['amount'],
                            'credit' => $result_wallet['detail']['credit'],
                            'payment_type' => 'card',
                            'info' => $account_info['info'],
                            'service_id' => $service_id,
                            'channel' => $account_info['channel'],
                            'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                            'language' => $account_info['language'],
                            'platform' => $account_info['platform'],
                            'receipt_data' => $result_withdraw['data']['receipt_data'],
                            'tracking_info' => $params['tracking_info'],
                            'device_id' => $params['device_id'],
                            'subtype' => $params['telco'],
                        );
                        $pay = $this->CI->MoboService->pay_to_service($arrPay);
                        if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                            $response = array(
                                'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                'credit' => intval($pay['credit']),
                                'unit' => $pay['unit'],
                                'money' => $pay['money'],
                                'service_data' => $pay['service_data']
                            );
                            return array("code" => 110, 'message' => 'REQUEST_SUCCESS', 'data' => $response);
                        } else {
                            MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data_finish, $arrPay, $deposit_transaction)), 'CARD_FAIL');
                            return array("code" => 111, 'message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction)));
                        }
                    } else {
                        MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data_finish, $deposit_transaction, $direct)), 'CARD_FAIL');
                        return array("code" => 111, 'message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction)));
                    }
                }
                $response = array(
                    'message' => $this->CI->Language->item('CARD_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                    'credit' => $result_wallet['detail']['credit'],
                    'balance' => $result_wallet['detail']['balance'],
                    'money' => $result_check_8595['amount'],
                    'transaction' => $deposit_transaction
                );
                return array("code" => 110, 'message' => "REQUEST_SUCCESS", "data" => $response);
            } else {
                MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($params, $data_finish, $deposit_transaction)), 'CARD_FAIL');
                return array("code" => 111, 'message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -5, 'transaction' => $deposit_transaction)));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('WALLET', array($params, $account_info, $blackbox, $deposit_transaction)), 'CARD_FAIL');
            return array("code" => 111, 'message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction)));
        }
    }

    public function verify_webmoney(MEAPI_RequestInterface $request, $account_info) {
        $this->CI->load->MEAPI_Model('DepositModel');
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);

        $result_check_webmoney = array(
            'amount' => $params['money']
        );

        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
        $deposit_transaction = strtoupper('dwm' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));
        $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $account_info['mobo_id'], SCOPE_ID, $result_check_webmoney['amount'], 'ewallet', 'wmoney');

        if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($result_check_webmoney, $account_info, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
        }
        $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
        $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);

        if ($result_wallet['code'] == 1) {
            if (empty($params['channel']) === FALSE) {
                $tmp_channel = explode('|', $params['channel']);
                $provider = $tmp_channel[0];
                $channel = $params['channel'];
            } else {
                $provider = 0;
                $channel = '0|error';
            }
            $data_finish = array(
                'mobo_id' => $account_info['mobo_id'],
                'money' => $result_check_webmoney['amount'],
                'credit' => $result_wallet['detail']['credit'],
                'type' => 'card',
                'blackbox_transaction' => $blackbox['blackbox_transaction'],
                'deposit_transaction' => $deposit_transaction,
                'channel' => $channel,
                'provider' => $provider,
                'scope_id' => SCOPE_ID,
            );
            $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
            if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                if (empty($params['direct']) === FALSE) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $direct = array(
                        'credit' => $result_wallet['detail']['credit']
                    );
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                    $result_withdraw = $output->getArray();
                    if ($result_withdraw['code'] == 110) {
                        $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                        $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                        $arrPay = array(
                            'mobo_id' => $account_info['mobo_id'],
                            'money' => $result_check_webmoney['amount'],
                            'credit' => $result_wallet['detail']['credit'],
                            'payment_type' => 'ewallet',
                            'info' => $params['info'],
                            'service_id' => SCOPE_ID,
                            'channel' => $params['channel'],
                            'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                            'language' => $params['language'],
                            'platform' => $params['platform'],
                            'receipt_data' => $result_withdraw['data']['receipt_data'],
                            'tracking_info' => $params['tracking_info'],
                            'device_id' => $params['device_id'],
                            'subtype' => 'webmoney',
                        );
                        $pay = $this->CI->MoboService->pay_to_service($arrPay);
                        if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                            $response = array(
                                'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                                'credit' => intval($pay['credit']),
                                'unit' => $pay['unit'],
                                'money' => $pay['money'],
                                'service_data' => $pay['service_data']
                            );
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        } else {
                            MEAPI_Log::writeCsv(merge_log('PAY TO GAME', array($data_finish, $arrPay, $deposit_transaction)), 'CARD_FAIL');
                            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                        }
                    } else {
                        MEAPI_Log::writeCsv(merge_log('WITHDRAW FAIL', array($data_finish, $deposit_transaction, $direct)), 'CARD_FAIL');
                        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -7, 'transaction' => $deposit_transaction))));
                    }
                }
                $response = array(
                    'message' => $this->CI->Language->item('CARD_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                    'credit' => $result_wallet['detail']['credit'],
                    'balance' => $result_wallet['detail']['balance'],
                    'money' => $result_check_webmoney['amount'],
                    'transaction' => $deposit_transaction
                );
                return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
            } else {
                MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($params, $data_finish, $deposit_transaction)), 'CARD_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -5, 'transaction' => $deposit_transaction))));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('WALLET', array($params, $account_info, $blackbox, $deposit_transaction)), 'CARD_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('CARD_FAIL', array('error_code' => -2, 'transaction' => $deposit_transaction))));
        }
    }

}
