<?php

class MEAPI_Controller_DepositController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_DepositInterface {

    public function update_mt($sms_connection_id, $mo_id, $mt, $cdr) {
        $this->CI->load->MEAPI_Model('DepositModel');
        $this->CI->DepositModel->update_mt_by_mo_id($sms_connection_id, $mo_id, $mt, $cdr);
    }

    public function sms_receive($sms_gateway, MEAPI_RequestInterface $request, $direct = array()) {
        $this->CI->load->MEAPI_Library('SmsGateway/SMS_' . $sms_gateway, 'sms_gateway');
        $params = $request->input_request();
        $params = array_merge($params, $direct);
        $this->CI->sms_gateway->set_params($params);
        $this->CI->load->MEAPI_Model('DepositModel');
        if ($this->CI->sms_gateway->verify_token() === TRUE) {
            $sms_info = $this->CI->sms_gateway->get_data();
            if (is_array($sms_info) === TRUE) {
                $this->CI->load->MEAPI_Library('Mopay/MPayment', 'MPayment');
                $sms_transaction_id = $this->CI->sms_gateway->get_sms_transaction_id();
                if (empty($sms_transaction_id) === TRUE) {
                    $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-1)', 0, $sms_info, $this);
                    MEAPI_Log::writeCsv(merge_log('SMS (-1)', $sms_info), 'DEPOSIT_SMS_FAIL');
                    return $this->CI->sms_gateway->response_fail();
                }
                $sms_transaction_info = $this->CI->MPayment->get_sms_transaction($this->CI->sms_gateway->get_mobo_id(), $this->CI->sms_gateway->get_sms_transaction_id());
                if (empty($sms_transaction_info) === TRUE) {
                    $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-2)', 0, $sms_info, $this);
                    MEAPI_Log::writeCsv(merge_log('SMS (-2)', $sms_info), 'DEPOSIT_SMS_FAIL');
                    return $this->CI->sms_gateway->response_fail();
                }
                $check_duplicate = $this->CI->DepositModel->check_mo_id($this->CI->sms_gateway->get_connection_id(), $sms_info['mo_id']);
                if (empty($check_duplicate) === FALSE) {
                    $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-3)', 0, $sms_info, $this);
                    MEAPI_Log::writeCsv(merge_log('SMS (-3)', $sms_info), 'DEPOSIT_SMS_FAIL');
                    return $this->CI->sms_gateway->response_fail();
                }
                if (!in_array($this->CI->sms_gateway->get_price(), array(20000, 50000, 100000))) {
                    $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-10)', 0, $sms_info, $this);
                    MEAPI_Log::writeCsv(merge_log('SMS (-10)', $sms_info), 'DEPOSIT_SMS_FAIL');
                    return $this->CI->sms_gateway->response_fail();
                }
                if ($params['mode'] == 'CHECK') {
                    $this->CI->sms_gateway->send_mt('Hop le', 1, $sms_info, $this);
                    return $this->CI->sms_gateway->response_success();
                }
                $deposit_transaction = strtoupper('ds' . dechex($sms_transaction_info['mobo_id'] . rand(1111111, 9999999)));
                $data = array(
                    'mobo_id' => $this->CI->sms_gateway->get_mobo_id(),
                    'sms_connection_id' => $this->CI->sms_gateway->get_connection_id(),
                    'mo_id' => $sms_info['mo_id'],
                    'money' => $this->CI->sms_gateway->get_price($params['servicenum']),
                    'mo' => $sms_info['content'],
                    'telco' => $sms_info['telco'],
                    'phone' => $sms_info['phone'],
                    'service_number' => $sms_info['service_number'],
                    'code' => $sms_info['code'],
                    'received_time' => $sms_info['received_time'],
                    'sms_transaction_id' => $this->CI->sms_gateway->get_sms_transaction_id(),
                    'ip_called' => $_SERVER['REMOTE_ADDR'],
                    'ip_user' => $sms_transaction_info['ip'],
                    'language' => $sms_transaction_info['language'],
                    'user_agent' => $sms_transaction_info['user_agent'],
                    'platform' => $sms_transaction_info['platform'],
                    'deposit_transaction' => $deposit_transaction
                );
                $result = $this->CI->DepositModel->insert_deposit_sms($data);
                if (is_array($result) && $result['code'] == 1) {
                    $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
                    $blackbox = $this->CI->MBlackbox->take_transaction_deposit($deposit_transaction, $data['mobo_id'], $sms_transaction_info['scope_id'], $data['money'], 'sms', $data['service_number']);
                    if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
                        $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-4). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                        MEAPI_Log::writeCsv(merge_log('SMS (-4)', array($data, $sms_info)), 'DEPOSIT_SMS_FAIL');
                        return $this->CI->sms_gateway->response_fail();
                    }
                    $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
                    $result_wallet = $this->CI->MWallet->deposit($data['mobo_id'], $blackbox['blackbox_transaction']);
                    if ($result_wallet['code'] == 1) {
                        if (empty($sms_transaction_info['channel']) === FALSE) {
                            $tmp_channel = explode('|', $sms_transaction_info['channel']);
                            $provider = $tmp_channel[0];
                            $channel = $sms_transaction_info['channel'];
                        } else {
                            $provider = 0;
                            $channel = '0|error';
                        }
                        $data_finish = array(
                            'mobo_id' => $data['mobo_id'],
                            'money' => $data['money'],
                            'credit' => $result_wallet['detail']['credit'],
                            'type' => 'sms',
                            'blackbox_transaction' => $blackbox['blackbox_transaction'],
                            'deposit_transaction' => $data['deposit_transaction'],
                            'channel' => $channel,
                            'provider' => $provider,
                            'scope_id' => $sms_transaction_info['scope_id'],
                        );
                        $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
                        if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                            if (empty($sms_transaction_info['direct']) === FALSE) {
                                $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                                $direct = array(
                                    'credit' => $result_wallet['detail']['credit']
                                );
                                $direct = array_merge($direct, $sms_transaction_info);
                                $account_info = array(
                                    'mobo_id' => $data['mobo_id']
                                );
                                $output = $this->CI->MWithdraw->withdraw($request, $account_info, $direct);
                                $arrOutput = $output->getArray();
                                if ($arrOutput['code'] == 110) {
                                    $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                                    $arrPay = array(
                                        'mobo_id' => $data['mobo_id'],
                                        'money' => $data['money'],
                                        'credit' => $result_wallet['detail']['credit'],
                                        'payment_type' => 'sms',
                                        'info' => $sms_transaction_info['info'],
                                        'service_id' => $sms_transaction_info['scope_id'],
                                        'channel' => $sms_transaction_info['channel'],
                                        'mobo_service_id' => $sms_transaction_info['mobo_service_id'],
                                        'platform' => $sms_transaction_info['platform'],
                                        'language' => $sms_transaction_info['language'],
                                        'receipt_data' => $arrOutput['data']['receipt_data']
                                    );
                                    $pay = $this->CI->MoboService->pay_to_service($arrPay);
                                    if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                                        $this->CI->sms_gateway->send_mt($pay['message'], 1, $sms_info, $this);
                                        return $this->CI->sms_gateway->response_success();
                                    } else {
                                        $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-9). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                                        MEAPI_Log::writeCsv(merge_log('SMS (-9)', array($data, $sms_info, $arrPay)), 'DEPOSIT_SMS_FAIL');
                                        return $this->CI->sms_gateway->response_fail();
                                    }
                                } else {
                                    $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-8). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                                    MEAPI_Log::writeCsv(merge_log('SMS (-9)', array($data, $sms_info, $account_info)), 'DEPOSIT_SMS_FAIL');
                                    return $this->CI->sms_gateway->response_fail();
                                }
                            }
                            $this->CI->sms_gateway->send_mt('Nap thanh cong ' . $result_wallet['detail']['credit'] . ' mcoin. So mcoin hien tai cua ban la ' . $result_wallet['detail']['balance'] . ' mcoin', 1, $sms_info, $this);
                            return $this->CI->sms_gateway->response_success();
                        } else {
                            $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-5). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                            MEAPI_Log::writeCsv(merge_log('SMS (-5)', array($data, $sms_info, $data_finish)), 'DEPOSIT_SMS_FAIL');
                            return $this->CI->sms_gateway->response_fail($result_finish_deposit['detail']);
                        }

                    } else {
                        $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-6). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                        MEAPI_Log::writeCsv(merge_log('SMS (-6)', array($data, $sms_info)), 'DEPOSIT_SMS_FAIL');
                        return $this->CI->sms_gateway->response_fail();
                    }
                } else {
                    $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-7). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                    MEAPI_Log::writeCsv(merge_log('SMS (-7)', array($data, $sms_info)), 'DEPOSIT_SMS_FAIL');
                    return $this->CI->sms_gateway->response_fail();
                }
                $this->CI->sms_gateway->send_mt('Nap mcoin that bat (-99). Ma gd ' . $deposit_transaction, 0, $sms_info, $this);
                MEAPI_Log::writeCsv(merge_log('SMS (-99)', array($data, $sms_info)), 'DEPOSIT_SMS_FAIL');
                return $this->CI->sms_gateway->response_fail();
            } else {
                return $this->CI->sms_gateway->response_invalid_data();
            }
        } else {
            return $this->CI->sms_gateway->response_invalid_token();
        }
    }

    public function sms_receive_7x65(MEAPI_RequestInterface $request) {
        $this->sms_receive('7x65', $request);
    }


    public function sms_receive_9029(MEAPI_RequestInterface $request) {
        $this->sms_receive('9029', $request);
    }

    public function sms_receive_9029_new(MEAPI_RequestInterface $request) {
        $this->sms_receive('9029_new', $request);
    }

    public function sms_receive_7x65_direct(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE || TRUE) {
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {

                $direct = 0;
                if (empty($params['direct']) === FALSE) {
                    $direct = 1;
                }
                $sms_transaction_id = dechex(rand(1111, 9999999));
                $arrInsert = array(
                    'language' => 'vn',
                    'user_agent' => 'direct_call',
                    'platform' => $params['platform'],
                    'sms_transaction_id' => $sms_transaction_id,
                    'mobo_id' => $params['mobo_id'],
                    'service_number' => $params['servicenum'],
                    'scope_id' => $params['scope_id'],
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'direct' => $direct,
                    'data' => $params['info'],
                    'channel' => '1|direct',
                    'mobo_service_id' => $params['mobo_service_id']
                );
                $this->CI->load->MEAPI_Model('PaymentModel');
                $direct = array();
                $result = $this->CI->PaymentModel->insert_sms_transaction($arrInsert);
                if (is_array($result) && $result['code'] == 1) {
                    $direct['request'] = 'MOPAY1 ' . $params['mobo_id'] . ' ' . $sms_transaction_id;
                    $this->sms_receive('7x65', $request, $direct);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $response);
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function top(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array('limit');
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params, array('type', 'ip', 'mobo_id'));
                $historys = $this->CI->DepositModel->top($filter, $params['offset'], $params['limit'], $params['from'], $params['to']);
                if ($historys['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $historys['detail']);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function history(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('mobo_id', 'offset', 'limit');
            if (is_required($params, $needle) == TRUE) {
                $account_info = array(
                    'mobo_id' => $params['mobo_id']
                );
                $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                $this->_response = $this->CI->MDeposit->history($request, $account_info);

            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function report(MEAPI_RequestInterface $request) {
        //TODO: Thêm cột IP vào bảng deposits
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params, array('type', 'ip'));
                $historys = $this->CI->DepositModel->report($filter, $params['from'], $params['to']);
                if ($historys['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $historys['detail']);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function new_credit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == TRUE) {
                $historys = $this->CI->DepositModel->new_credit($params['from'], $params['to']);
                if ($historys['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $historys['detail']);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function report_detail(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == TRUE) {
                $card = $this->CI->DepositModel->history_card($params['from'], $params['to']);
                $bank = $this->CI->DepositModel->history_bank($params['from'], $params['to']);
                $sms = $this->CI->DepositModel->history_sms($params['from'], $params['to']);
                $data = array(
                    'card' => $card['detail'],
                    'sms' => $sms['detail'],
                    'banking' => $bank['detail']
                );
                if ($card['code'] == 1 || $sms['code'] == 1 || $bank['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $data);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function topup_sandbox(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Language');
                $this->CI->Language->init($params['language']);
                $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                $arrPay = array(
                    'mobo_id' => $params['mobo_id'],
                    'money' => $params['money'],
                    'credit' => $result_wallet['detail']['credit'],
                    'payment_type' => 'card',
                    'info' => $params['info'],
                    'service_id' => SCOPE_ID,
                    'channel' => $params['channel'],
                    'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                    'language' => $params['language'],
                    'platform' => $params['platform'],
                    'receipt_data' => $result_withdraw['data']['receipt_data']
                );
                $pay = $this->CI->MoboService->pay_to_service($arrPay);
                if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                    $response = array(
                        'message' => $this->CI->Language->item('PAYMENT_SUCCESS'),
                        'credit' => intval($pay['credit']),
                        'unit' => $pay['unit'],
                        'money' => $pay['money']
                    );
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                } else {
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                    return;
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
}
