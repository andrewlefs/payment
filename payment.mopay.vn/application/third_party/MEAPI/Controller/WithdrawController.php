<?php

class MEAPI_Controller_WithdrawController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_WithdrawInterface {

    public function top(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('WithdrawModel');
            $params = $request->input_request();
            $needle = array('limit');
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params,array('service_id'));
                $historys = $this->CI->WithdrawModel->top($filter, $params['offset'], $params['limit'], $params['from'], $params['to']);
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
                $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                $this->_response = $this->CI->MWithdraw->history($request, $account_info);

            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function report(MEAPI_RequestInterface $request) {
        //TODO: Bảng withdraw thêm vào platform
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('WithdrawModel');
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {
				$params['scope_id'] = $params['service_id'];
                $filter = make_array($params,array('platform','service_id','scope_id'));				
                $historys = $this->CI->WithdrawModel->report($filter, $params['from'], $params['to']);
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
        //TODO: Bảng withdraw thêm vào platform
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('WithdrawModel');
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params,array('from','to'));
                $historys = $this->CI->WithdrawModel->report_detail($params['from'], $params['to']);
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
