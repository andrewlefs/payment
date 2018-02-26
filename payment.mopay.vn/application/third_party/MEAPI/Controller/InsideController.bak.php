<?php

class MEAPI_Controller_InsideController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_InsideInterface {

    public function get_balance(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('mobo_id');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $result['rows'] = $this->CI->InsideModel->get_balance($params['mobo_id']);

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }

        } else {
            $this->_response = $authorize->getResponse();
        }
    }


    public function get_top_deposit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            if (valid_range_date($params['from'], $params['to']) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_RANGE_DATE');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $offset = empty($params['offset']) ? 0 : $params['offset'];
            $limit = empty($params['limit']) ? LIMIT_PAGE : $params['limit'];

            $result['rows'] = $this->CI->InsideModel->get_top_deposit($params['from'], $params['to'], $offset, $limit, $params['type']);
			$result['total_rows'] = $this->CI->InsideModel->total_rows();
			
            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_top_withdraw(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            if (valid_range_date($params['from'], $params['to']) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_RANGE_DATE');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $offset = empty($params['offset']) ? 0 : $params['offset'];
            $limit = empty($params['limit']) ? LIMIT_PAGE : $params['limit'];

            $result['rows'] = $this->CI->InsideModel->get_top_withdraw($params['from'], $params['to'], $offset, $limit, $params['type']);

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function deposit_history(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            if (valid_range_date($params['from'], $params['to']) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_RANGE_DATE');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $offset = empty($params['offset']) ? 0 : $params['offset'];
            $limit = empty($params['limit']) ? LIMIT_PAGE : $params['limit'];

            $result['rows'] = $this->CI->InsideModel->deposit_history($params['from'], $params['to'], $offset, $limit, $params['type']);
            $result['total_rows'] = $this->CI->InsideModel->total_rows();

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }

        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function wallet_history(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            if (valid_range_date($params['from'], $params['to']) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_RANGE_DATE');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $offset = empty($params['offset']) ? 0 : $params['offset'];
            $limit = empty($params['limit']) ? LIMIT_PAGE : $params['limit'];

            $result['rows'] = $this->CI->InsideModel->wallet_history($params['from'], $params['to'], $offset, $limit);
            $result['total_rows'] = $this->CI->InsideModel->total_rows();

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }

        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function withdraw_history(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            if (valid_range_date($params['from'], $params['to']) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_RANGE_DATE');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $offset = empty($params['offset']) ? 0 : $params['offset'];
            $limit = empty($params['limit']) ? LIMIT_PAGE : $params['limit'];

            $result['rows'] = $this->CI->InsideModel->withdraw_history($params['from'], $params['to'], $offset, $limit);
            $result['total_rows'] = $this->CI->InsideModel->total_rows();

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }

        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function report_deposit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $result['rows'] = $this->CI->InsideModel->report_deposit($params['from'], $params['to'], $params['type']);
            $result['total_rows'] = $this->CI->InsideModel->total_rows();

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function report_withdraw(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $result['rows'] = $this->CI->InsideModel->report_withdraw($params['from'], $params['to']);
            $result['total_rows'] = $this->CI->InsideModel->total_rows();

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function report_wallet(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();
            $needle = array('from', 'to');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Model('InsideModel');

            $result['rows'] = $this->CI->InsideModel->report_deposit($params['from'], $params['to']);
            $result['total_rows'] = $this->CI->InsideModel->total_rows();

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
}
