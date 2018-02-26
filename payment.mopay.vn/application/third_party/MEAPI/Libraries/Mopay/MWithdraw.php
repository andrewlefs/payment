<?php

class MWithdraw {
    /**
     * @var CI_Controller
     */
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function history(MEAPI_RequestInterface $request, $account_info) {

        $this->CI->load->MEAPI_Library('CardGateway/Card_mecorp', 'card_gateway');
        $this->CI->load->MEAPI_Model('WithdrawModel');
        $params = $request->input_request();
        $historys = $this->CI->WithdrawModel->history($account_info['mobo_id'], $params['offset'], $params['limit'], $params['from'], $params['to']);
        if ($historys['code'] == 1) {
            return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $historys['detail']);
        } else {
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
        }
    }

    public function withdraw(MEAPI_RequestInterface $request, $account_info, $direct = NULL) {
        $this->CI->load->MEAPI_Model('WithdrawModel');
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->Language->init($params['language']);
        if (is_array($direct) === TRUE) {
            $params = array_merge($params, $direct);
        }
        $withdraw_transaction = strtoupper('wd' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)));

        $data = array(
            'mobo_id' => $account_info['mobo_id'],
            'credit' => $params['credit'],
            'withdraw_transaction' => $withdraw_transaction,
            'ip_called' => $_SERVER['REMOTE_ADDR'],
            'ip_user' => $params['ip'],
            'language' => $params['language'],
            'user_agent' => $params['user_agent'],
            'platform' => $params['platform'],
            'service_id' => $params['service_id']
        );

        $service_id = !empty($account_info['service_id'])?$account_info['service_id']:SCOPE_ID;
        $result = $this->CI->WithdrawModel->insert_withdraw_history($data);
        if (is_array($result) && $result['code'] == 1) {
            $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
            $blackbox = $this->CI->MBlackbox->take_transaction_withdraw($withdraw_transaction, $account_info['mobo_id'], $service_id, $params['credit']);
            if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
                MEAPI_Log::writeCsv(array('BLACKBOX', $withdraw_transaction, $account_info['mobo_id'], $service_id, $params['credit']), 'WITHDRAW_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('WITHDRAW_FAIL', array('error_code' => -1, 'transaction' => $withdraw_transaction)), 'transaction' => $withdraw_transaction));
            }

            $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
            $result_wallet = $this->CI->MWallet->withdraw($account_info['mobo_id'], $blackbox['blackbox_transaction']);
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
                    'blackbox_transaction' => $blackbox['blackbox_transaction'],
                    'withdraw_transaction' => $withdraw_transaction,
                    'credit' => $params['credit'],
                    'result_item' => $params['result_string'],
                    'channel' => $channel,
                    'provider' => $provider,
                    'scope_id' => $service_id,
                    'service_id' => $params['service_id']
                );
                $result_finish_withdraw = $this->CI->WithdrawModel->finish_withdraw($data_finish);
                if (is_array($result_finish_withdraw) && $result_finish_withdraw['code'] == 1) {
                    $this->CI->load->library('Crypt');
                    $response = array(
                        'message' => $this->CI->Language->item('WITHDRAW_SUCCESS', array('credit' => $result_wallet['detail']['credit'], 'balance' => $result_wallet['detail']['balance'])),
                        'receipt_data' => array(
                            'credit' => $params['credit'],
                            'mobo_id' => $account_info['mobo_id'],
                            'transaction' => $withdraw_transaction
                        ));
                    $signature = md5(implode('', $response['receipt_data']) . MEAPI_Config_Mopay::withdraw_secret());
                    $response['receipt_data']['signature'] = $signature;
                    $response['receipt_data'] = base64_encode(json_encode($response['receipt_data']));

                    return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                } else {
                    MEAPI_Log::writeCsv(merge_log('FINISH_WITHDRAW',array( $withdraw_transaction, $account_info,$data,$data_finish)), 'WITHDRAW_FAIL');
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('WITHDRAW_FAIL', array('error_code' => -2, 'transaction' => $withdraw_transaction)), 'transaction' => $withdraw_transaction));
                }

            } elseif ($result_wallet['code'] == -1) {
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('WITHDRAW_BALANCE_NOT_ENOUGHT', array('error_code' => -3, 'transaction' => $withdraw_transaction)), 'transaction' => $withdraw_transaction));
            } else {
                MEAPI_Log::writeCsv(merge_log('WALLET', array($withdraw_transaction, $account_info, $blackbox, $data)), 'WITHDRAW_FAIL');
                return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('WITHDRAW_FAIL', array('error_code' => -4, 'transaction' => $withdraw_transaction)), 'transaction' => $withdraw_transaction));
            }
        } else {
            MEAPI_Log::writeCsv(merge_log('WITHDRAW_INSERT', array($withdraw_transaction, $account_info, $data)), 'WITHDRAW_FAIL');
            return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('WITHDRAW_FAIL', array('error_code' => -5, 'transaction' => $withdraw_transaction)), 'transaction' => $withdraw_transaction));
        }
    }

    public function verify_transaction(MEAPI_RequestInterface $request) {
        $this->CI->load->MEAPI_Model('WithdrawModel');
        $params = $request->input_request();
        $transaction_info = json_decode(base64_decode($params['receipt_data']), TRUE);
        if (is_array($transaction_info) === TRUE) {
            if (empty($transaction_info['signature']) === FALSE) {
                $signature = $transaction_info['signature'];
                unset($transaction_info['signature']);
                if ($signature == md5(implode('', $transaction_info) . MEAPI_Config_Mopay::withdraw_secret())) {
                    $this->CI->WithdrawModel->verify_transaction($transaction_info['transaction'], $params['result_string'], $params['service_id']);
                    return new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $transaction_info);
                }
            }


        }
        return new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Mã giao dịch không tồn tại'));
    }
}