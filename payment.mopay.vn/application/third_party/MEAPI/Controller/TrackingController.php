<?php

class MEAPI_Controller_TrackingController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_TrackingInterface {

    public function deposit_card(MEAPI_RequestInterface $request){		
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array();            
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params,array('mobo_id','serial','pin','deposit_transaction'));                
                if($params['limit']){
                    $tracking = $this->CI->DepositModel->tracking_card($filter,$params['offset'],$params['limit']);
                }else{
                    $tracking = $this->CI->DepositModel->tracking_card($filter);
                }
                if ($tracking['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $tracking['detail']);
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

    public function deposit_sms(MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params,array('mobo_id','phone','deposit_transaction'));
                if($params['limit']){
                    $tracking = $this->CI->DepositModel->tracking_sms($filter,$params['offset'],$params['limit']);
                }else{
                    $tracking = $this->CI->DepositModel->tracking_sms($filter);
                }                
                if ($tracking['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $tracking['detail']);
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

    public function deposit_banking(MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $this->CI->load->MEAPI_Model('DepositModel');
            $params = $request->input_request();
            $needle = array();
            if (is_required($params, $needle) == TRUE) {
                $filter = make_array($params,array('mobo_id','bank_transaction','deposit_transaction'));
                if($params['limit']){
                    $tracking = $this->CI->DepositModel->tracking_banking($filter,$params['offset'],$params['limit']);
                }else{
                    $tracking = $this->CI->DepositModel->tracking_banking($filter);
                }                
                if ($tracking['code'] == 1) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $tracking['detail']);
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
    
    public function withdraw_status(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {

            $params = $request->input_request();
            $needle = array('transaction_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->get_withdraw_history($where);
                if (empty($result) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
    
    
    public function deposit_status(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {

            $params = $request->input_request();
            $needle = array('transaction_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->get_deposit_history($where);
                if (empty($result) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
    
    public function partner_list(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {

            $params = $request->input_request();            
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');                
                $result[] = array(
                    'partner_id' => 1,
                    'partner_name' => 'ME'
                );
                if (empty($result) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
}
