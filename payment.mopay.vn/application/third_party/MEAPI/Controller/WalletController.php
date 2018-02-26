<?php

class MEAPI_Controller_WalletController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_WalletInterface {

    protected $_response;

    public function deposit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('mobo_id', 'blackbox_transaction');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('WalletModel');
                $result = $this->CI->WalletModel->insert_wallet_request($params['blackbox_transaction'], $params['mobo_id'], 'deposit');
                if ($result['code'] == 1) {
                    $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
                    $blackbox_info = $this->CI->MBlackbox->get_transaction_deposit($params['blackbox_transaction']);
                    if (empty($blackbox_info) === TRUE || intval($blackbox_info['credit'] < 1)) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'BLACKBOX_TRANSACTION_NOT_EXIST');
                        return;
                    }
                    $result = $this->CI->WalletModel->deposit($params['blackbox_transaction'], $params['mobo_id'], $blackbox_info['credit']);
                    if ($result['code'] == 1) {
                        $response = array('credit' => $blackbox_info['credit'], 'money' => $blackbox_info['money'], 'balance' => $result['balance']);
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        return;
                    }
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $result['detail']);
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
            $needle = array('mobo_id', 'blackbox_transaction');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('WalletModel');
                $result = $this->CI->WalletModel->insert_wallet_request($params['blackbox_transaction'], $params['mobo_id'], 'withdraw');
                if ($result['code'] == 1) {
                    $this->CI->load->MEAPI_Library('Mopay/MBlackbox', 'MBlackbox');
                    $blackbox_info = $this->CI->MBlackbox->get_transaction_withdraw($params['blackbox_transaction']);
                    if (empty($blackbox_info) === TRUE || intval($blackbox_info['credit'] < 1)) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'BLACKBOX_TRANSACTION_NOT_EXIST');
                        return;
                    }
                    $result = $this->CI->WalletModel->withdraw($params['blackbox_transaction'], $params['mobo_id'], $blackbox_info['credit']);
                    if ($result['code'] == 1) {
                        $response = array('credit' => $blackbox_info['credit'], 'balance' => intval($result['balance']));
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        return;
                    } elseif ($result['code'] == -1) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'BALANCE_NOT_ENOUGHT');
                        return;
                    }
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $result['detail']);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function balance(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('mobo_id');
            if (is_required($params, $needle) == TRUE) {				
                $this->CI->load->MEAPI_Model('WalletModel');
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $mobo_info = $this->CI->MoboGraphAPI->search_graph($params['mobo_id']);                				
				if ($mobo_info['status'] == 1) {
                    foreach ($mobo_info['data'] as $key => $value) {
                        $mobo_id = $value[0]['mobo_id'];
                        break;
                    }
                } else {
                    $mobo_id = $params['mobo_id'];
                }					
                $response = $this->CI->WalletModel->balance($mobo_id);				
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
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
            $this->CI->load->MEAPI_Model('WalletModel');
            $params = $request->input_request();
            $needle = array('limit', 'filter_type');
            if (is_required($params, $needle) == TRUE) {
                $historys = $this->CI->WalletModel->top($params['filter_type'], $params['offset'], $params['limit'], $params['from'], $params['to']);
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

    public function report(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('WalletModel');
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {
                $historys = $this->CI->WalletModel->report();
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
                $this->CI->load->MEAPI_Model('WalletModel');
                $historys = $this->CI->WalletModel->history($params['mobo_id'], $params['offset'], $params['limit'], $params['from'], $params['to']);
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

}
