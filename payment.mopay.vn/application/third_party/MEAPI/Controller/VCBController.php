<?php

class MEAPI_Controller_VCBController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_VCBInterface {


    public function active(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('vcb_trans', 'account', 'vcb_id', 'trans_datetime');
            if (is_required($params, $needle) == TRUE) {

//                if ($params['account'] == '0909000200') {
//                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_SUCCESS', $response);
//                    return;
//                }
//                if ($params['account'] == '0909000201') {
//                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_FAIL', $response);
//                    return;
//                }
//                if ($params['account'] == '0909000202') {
//                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST', $response);
//                    return;
//                }
//                if ($params['account'] == '0909000203') {
//                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_EXIST', $response);
//                    return;
//                }

                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');

                $account_info = $this->CI->MoboGraphAPI->get_account($params['account']);
                if (empty($account_info) == TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST');
                    return;
                }

                $this->CI->load->MEAPI_Model('PaymentModel');
                $check = $this->CI->PaymentModel->check_vcb_service($account_info['mobo_id']);
                if (empty($check) === FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_EXIST');
                    return;
                }
                $arrInsert = array(
                    'phone' => $account_info['phone'],
                    'mobo_id' => $account_info['mobo_id'],
                    'vcb_id' => $params['vcb_id'],
                    'vcb_transaction' => $params['vcb_trans']
                );
                $this->CI->load->MEAPI_Model('PaymentModel');
                $result = $this->CI->PaymentModel->active_vcb($arrInsert);
                if (empty($result) === FALSE) {
                    $response['account_id'] = $account_info['mobo_id'];
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_SUCCESS', $response);
                }else{
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_FAIL', $response);
                }

            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function check_active(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('vcb_trans', 'account');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('PaymentModel');
                $check = $this->CI->PaymentModel->check_vcb_service($params['account']);
                if (empty($check) === TRUE) {
                    $response['fullname'] = $params['account'];
                    $response['address'] = $params['account'];
                    $response['id_number'] = $params['account'];
                    $response['id_number_type'] = 'IC';
                    $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_SUCCESS');
                    return;
                }else{
                    $response['fullname'] = $params['account'];
                    $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_FAIL', $response);
                    return;
                }
                /*
                if ($params['account'] == '0909000200') {
                    $response['fullname'] = 'Thương HH';
                    $response['address'] = '141 Ly Chinh Thang';
                    $response['id_number'] = '027594739';
                    $response['id_number_type'] = 'IC';

                    $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_SUCCESS', $response);
                    return;
                }
                if ($params['account'] == '0909000201') {
                    $response['fullname'] = 'Thương HH';
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_FAIL', $response);
                    return;
                }
                if ($params['account'] == '0909000202') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST', $response);
                    return;
                }
                if ($params['account'] == '0909000203') {
                    $response['fullname'] = 'Thương HH';
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_EXIST', $response);
                    return;
                }
                */

            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_active(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('vcb_trans', 'account');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('PaymentModel');
                $check = $this->CI->PaymentModel->check_vcb_service($params['account']);
                if (empty($check) === FALSE) {
                    $response['fullname'] = $params['account'];
                    $response['address'] = $params['account'];
                    $response['id_number'] = $params['account'];
                    $response['id_number_type'] = 'IC';
                    $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_SUCCESS');
                    return;
                }else{
                    $response['fullname'] = $params['account'];
                    $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_FAIL', $response);
                    return;
                }

            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    /*
    public function verify_active(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('code_verify', 'identify');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->library('Crypt');
                $identify_info = json_decode($this->CI->crypt->Decrypt($params['identify'], 'mopay!@#vcb'), TRUE);
                //echo $identify_info['otp'];exit;
                if (empty($identify_info['mobo_id']) === TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'VERIFY_ACTIVE_INVALID');
                    return;
                }
                if ($identify_info['otp'] != $params['code_verify']) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'VERIFY_ACTIVE_INVALID');
                    return;
                }
                $arrInsert = array(
                    'mobo_id' => $identify_info['mobo_id'],
                    'vcb_id' => $identify_info['vcb_id'],
                    'vcb_transaction' => $identify_info['vcb_trans']
                );
                $this->CI->load->MEAPI_Model('PaymentModel');
                $result = $this->CI->PaymentModel->active_vcb($arrInsert);
                if (empty($result) === FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'VERIFY_ACTIVE_SUCCESS', array('account_id' => $arrInsert['mobo_id']));
                    return;
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'VERIFY_ACTIVE_FAIL');
                    return;
                }
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
    */

    public function deactive(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('vcb_trans', 'account', 'vcb_id', 'trans_datetime');
            if (is_required($params, $needle) == TRUE) {
                if ($params['account'] == '0909000200') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'DEACTIVE_SUCCESS', $response);
                    return;
                }
                if ($params['account'] == '0909000201') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'DEACTIVE_FAIL', $response);
                    return;
                }
                $this->CI->load->MEAPI_Model('PaymentModel');
                $result = $this->CI->PaymentModel->deactive_vcb($params['vcb_id']);
                if (empty($result) === FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'DEACTIVE_SUCCESS');
                    return;
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'DEACTIVE_FAIL');
                    return;
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    /*
    public function topup(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('vcb_trans', 'account', 'money', 'trans_datetime');
            if (is_required($params, $needle) == TRUE) {
                if ($params['account'] == '0909000200') {
                    $response = array(
                        'transaction' => md5(mktime())
                    );
                    $this->_response = new MEAPI_Response_APIResponse($request, 'TOPUP_SUCCESS', $response);
                    return;
                }
                if ($params['account'] == '0909000201') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'TOPUP_FAIL', $response);
                    return;
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'TOPUP_FAIL');
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
    */

    public function get_info(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('account');
            if (is_required($params, $needle) == TRUE) {
                if ($params['account'] == '0909000200') {
                    $response = array(
                        'phone' => '0909000100',
                        'id_number' => '024126850',
                        'address' => '141 Ly Chinh Thang',
                        'mobo_id' => '111111111',
                        'fullname' => 'ThuongHH',
                        'id_number_type' => 'IC'
                    );
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    return;
                }
                if ($params['account'] == '0909000201') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $response);
                    return;
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function update_info(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('account');
            if (is_required($params, $needle) == TRUE) {
                if ($params['account'] == '0909000200') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    return;
                }
                if ($params['account'] == '0909000201') {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $response);
                    return;
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function withdraw(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform', 'access_token', 'credit');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                $this->CI->load->MEAPI_Model('PaymentModel');
                $check = $this->CI->PaymentModel->check_vcb_service($account_info['mobo_id']);
                if (empty($check) === TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL',array('message' => 'Tài khoản chưa kích hoạt dịch vụ rút tiền qua Vietcombank'));
                    return;
                }
				
                if (is_array($account_info)) {
                    if($params['credit'] > 50000){
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAILT', array('message' => 'Bạn chỉ được phép rút 5,000,000 VNĐ/ giao dịch'));
                        return;
                    }
                    if($params['credit'] < 500){
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAILT', array('message' => 'Số tiền rút phải lớn hơn 50,000 VNĐ/ giao dịch'));
                        return;
                    }
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info);
                    $result_withdraw = $output->getArray();
                    if ($result_withdraw['code'] == 110) {
                        $this->CI->load->MEAPI_Library('Vcb/Vcb_mecorp', 'Vcb_mecorp');
                        $data = array(
                            'phone' => $check['phone'],
                            'mobo_id' => $account_info['mobo_id'],
                            'money' => $params['credit'] * 100,
                            'credit' => $params['credit'],
                            'transaction_id' => strtoupper('WVCB' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)))
                        );
                        $result = $this->CI->Vcb_mecorp->deposit($data);
                        if ($result['status'] == 1) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', array('message' => 'Rút tiền thành công'));
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Nạp tiền không thành công ( Lỗi VCB: '. $result['message'] .' )'));
                        }
                    }else{
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => str_replace('mcoin','VNĐ',$result_withdraw['data']['message'])));
                        return;
                    }

                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function deposit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform', 'account', 'money');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $this->CI->load->MEAPI_Library('Language');
                $this->CI->Language->init($params['language']);
                $account_info = $this->CI->MoboGraphAPI->get_account($params['account']);
                if (empty($account_info) == TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST');
                    return;
                }
                $this->CI->load->MEAPI_Model('PaymentModel');
                $check = $this->CI->PaymentModel->check_vcb_service($account_info['mobo_id']);
                if (empty($check) === TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL',array('message' => 'Tài khoản chưa kích hoạt dịch vụ rút tiền qua Vietcombank'));
                    return;
                }
                if($params['money'] % 1000 > 0){
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL',array('message' => 'Số tiền muốn nạp phải chia hết cho 1000'));
                    return;
                }
                if($params['money'] > 50000000){
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL',array('message' => 'Bạn chỉ được phép nạp 50,000,000 VNĐ/ giao dịch'));
                    return;
                }
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Vcb/Vcb_mecorp', 'Vcb_mecorp');
                    $data = array(
                        'phone' => $check['phone'],
                        'mobo_id' => $account_info['mobo_id'],
                        'money' => $params['money'],
                        'credit' => 0,
                        'transaction_id' => strtoupper('DVCB' . dechex($account_info['mobo_id'] . rand(1111111, 9999999)))
                    );
                    $this->CI->load->MEAPI_Model('DepositModel');
                    $result = $this->CI->Vcb_mecorp->withdraw($data);
                    if ($result['status'] == 1) {
                        $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
                        $blackbox = $this->CI->MBlackbox->take_transaction_deposit($data['transaction_id'], $account_info['mobo_id'], SERVICE_ID, $params['money'], 'banking');
                        if (empty($blackbox) === TRUE || empty($blackbox['blackbox_transaction']) === TRUE) {
                            MEAPI_Log::writeCsv(merge_log('BLACKBOX', array($data, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -1, 'transaction' => $deposit_transaction))));
                            return;
                        }
                        $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
                        $result_wallet = $this->CI->MWallet->deposit($account_info['mobo_id'], $blackbox['blackbox_transaction']);
                        if ($result_wallet['code'] == 1) {
                            $data_finish = array(
                                'mobo_id' => $account_info['mobo_id'],
                                'money' => $params['money'],
                                'credit' => $result_wallet['detail']['credit'],
                                'type' => 'banking',
                                'blackbox_transaction' => $blackbox['blackbox_transaction'],
                                'deposit_transaction' => $data['transaction_id'],
                                'channel' => 0,
                                'provider' => 0,
                                'scope_id' => SERVICE_ID,
                            );
                            $result_finish_deposit = $this->CI->DepositModel->finish_deposit($data_finish);
                            if (is_array($result_finish_deposit) && $result_finish_deposit['code'] == 1) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', array('message' => 'Nạp thành công ' . $result_wallet['detail']['credit']*100 . ' VNĐ'));
                            } else {
                                MEAPI_Log::writeCsv(merge_log('FINISH_DEPOSIT', array($data_finish, $banking_info, $data, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
                                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => $this->CI->Language->item('BANKING_FAIL', array('error_code' => -6, 'transaction' => $deposit_transaction))));
                                return;
                            }

                        } else {
                            MEAPI_Log::writeCsv(merge_log('WALLET', array($data, $banking_info, $blackbox, $deposit_transaction)), 'BANKING_FAIL');
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Rút tiền không thành công'));
                            return;
                        }
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Nạp tiền không thành công ( Lỗi VCB: ' . $result['message'] .' )' ));
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
}
