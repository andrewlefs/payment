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

    public function get_withdraw_history(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {//if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

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
    
    
    public function get_deposit_history(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {//if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

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
    
    public function get_items(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('service_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->get_items($where);
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
    
    public function items_info(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('item_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->select_items($where);
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
    
    public function insert_items(\MEAPI_RequestInterface $request){        
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('item_id','item_name','credit','money','service_id','connection_id','message_success','message_fail','visible');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);                
                $result = $this->CI->InsideModel->insert_items($where);
                if ($result) {
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
    
    public function update_items(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('id','item_id','item_name','credit','money','message_success','message_fail','visible');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->update_items($where);
                if ($result) {
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
    
    public function delete_items(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('id','connection_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->delete_items($where);
                if ($result) {
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

	public function get_connections(\MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $this->CI->load->MEAPI_Model('InsideModel');
            $where = array('active' => 1);
            $result = $this->CI->InsideModel->get_connections($where);            
            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }
    
    public function get_apps(\MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('connection_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->get_apps($where);
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
    
    public function insert_app(\MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('id', 'code', 'name', 'group_id', 'conn_id', 'state', 'exchange', 'public_key', 'private_key');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->insert_app($where);
                if ($result) {
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
    
    public function update_app(\MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('id', 'name', 'group_id', 'state', 'exchange', 'public_key', 'private_key');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('InsideModel');
                $where = make_array($params, $needle);
                $result = $this->CI->InsideModel->update_app($where);
                if ($result) {
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


    /* API GET LIST PAYMENT ITEM GAME DEFAULT
    * param  app(number)
    */
    public function get_payment_itemgamedefault(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();

            $needle = array('app','service_id','connection_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                // $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $objConfigPay   =  $this->CI->ConfigPaymentModel->getPaymentItemGame(0,0);
                $dataItem ['method_payment_public']=   json_decode($objConfigPay['method_payment'],true);
                $dataItem ['value_payment_public'] =   json_decode($objConfigPay['value_payment'],true);
                $dataItem ['method_payment_internal']=   json_decode($objConfigPay['method_payment_me'],true);
                $dataItem ['value_payment_internal'] =   json_decode($objConfigPay['value_payment_me'],true);
                $dataItem ['method_payment_all']=   json_decode($objConfigPay['method_payment_ex'],true);
                $dataItem ['value_payment_all'] =   json_decode($objConfigPay['value_payment_ex'],true);
                if ($dataItem) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $dataItem);
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

    /*
* API GET LIST PAYMENT ITEM GAME
* param  app(number)
*/
    public function get_payment_itemgame(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();

            $needle = array('app','service_id','connection_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                // $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $objConfigPay   =  $this->CI->ConfigPaymentModel->getPaymentItemGame($params['service_id'],$params['connection_id']);
                $dataItem ['method_payment_public']=   json_decode($objConfigPay['method_payment'],true);
                $dataItem ['value_payment_public'] =   json_decode($objConfigPay['value_payment'],true);
                $dataItem ['method_payment_internal']=   json_decode($objConfigPay['method_payment_me'],true);
                $dataItem ['value_payment_internal'] =   json_decode($objConfigPay['value_payment_me'],true);
                $dataItem ['method_payment_all']=   json_decode($objConfigPay['method_payment_ex'],true);
                $dataItem ['value_payment_all'] =   json_decode($objConfigPay['value_payment_ex'],true);
                if ($dataItem) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $dataItem);
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




    /*
  * API GET LIST PAYMENT
  * param  app(number)
  */
    public function get_list_payment(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;

            $needle = array('app','connection_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $dataItem ['method_payment']=   json_decode($objConfigPay['key_payment'],true);
                $dataItem ['value_payment'] =   json_decode($objConfigPay['value_payment'],true);
                $this->CI->load->MEAPI_Library('ConfigPayment');
                $dataItem ['value_payment']   =   $this->CI->ConfigPayment->array_multisort($dataItem ['value_payment'],'sort','asc');
                $data   = array();
                if($dataItem['value_payment']) {
                    foreach ($dataItem ['value_payment'] as $key => $nRow) {
                        $data[$key] = array();
                        if (is_array($nRow)) {
                            $arrAll = array();
                            foreach ($nRow as $vkey => $vRow) {
                                if (!$vRow['is_active']) {
                                    $vRow['is_active'] = 'yes';
                                }
                                $arrAll[$vkey] = $vRow;

                            }
                            $data[$key] = $arrAll;
                        }
                    }
                    $dataItem['value_payment']= $data;
                }
                if ($dataItem) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $dataItem);
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
    public function get_list_payment_for_config(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;

            $needle = array('app','connection_id');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                $this->CI->load->MEAPI_Library('ConfigPayment');
                $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $dataItem ['method_payment']=   json_decode($objConfigPay['key_payment'],true);
                $dataItem ['value_payment'] =   json_decode($objConfigPay['value_payment'],true);
                $this->CI->load->MEAPI_Library('ConfigPayment');
                $dataItem ['value_payment']   =   $this->CI->ConfigPayment->array_multisort($dataItem ['value_payment'],'sort','asc');
                $data=array();
                $datadefault    = array();
                if($dataItem['value_payment']) {
                    foreach ($dataItem ['value_payment'] as $key => $nRow) {
                        $data[$key] = array();

                        if (is_array($nRow)) {
                            $arrPublic = array();
                            $arrInternal = array();
                            $arrAll = array();
                            foreach ($nRow as $vkey => $vRow) {
                                if($vRow['is_active']=='yes'){
                                    if ($vRow['view'] == 'public') {
                                        $arrPublic[$vkey] = $vRow;
                                        // $arrInternal[$vkey]= $vRow;
                                    } elseif ($vRow['view'] == 'internal') {
                                        $arrInternal[$vkey] = $vRow;
                                    } else {

                                        $arrAll[$vkey] = $vRow;
                                        $arrInternal[$vkey]= $vRow;
                                    }
                                }

                            }

                            $data[$key]['all'] =  $this->CI->ConfigPayment->compareSort($arrAll,'view','desc');
                            $data[$key]['public'] =  $this->CI->ConfigPayment->compareSort($arrPublic,'view','desc');
                            $data[$key]['internal'] = $this->CI->ConfigPayment->compareSort(array_merge($arrInternal, $arrPublic),'view','desc');
                            $datadefault[$key]['all'] =  $this->CI->ConfigPayment->compareSort($arrAll,'view','desc');
                            $datadefault[$key]['public'] =  $this->CI->ConfigPayment->compareSort($arrPublic,'view','desc');
                            $datadefault[$key]['internal'] =  $this->CI->ConfigPayment->compareSort(array_merge($arrInternal, $arrPublic),'view','desc');

                        }
                    }

                    $dataItem ['value_payment'] = $data;
                    $dataItem ['value_default'] = $datadefault;
                }
                if ($dataItem) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $dataItem);
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


    /*
     * API UPFATE CONFIG PAYMENT GAME
     * param  id_config(number)
     * param  app(number)
     * param  connection_id(number)
     * param  key_payment{tagret(mobo){_nodepaysms,_nodepaycard,_nodepaybank}}
    * param   key_value_payment = {
     *                       " _nodepaysms":["sms_1","sms_5","sms_10"],
     *                       " _nodepaycard":["gate","vms","vina","viettel"],
     *                        "_nodepaybank":{"acbbank":{"price":["vn_1","vn_10"]}}
     *                     }
     * param  is_active(yes/no)

     */
    public function update_configpay_itemgame(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();

            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;

            $needle = array('service_id','connection_id','method_payment','value_payment','method_payment_me','value_payment_me','method_payment_ex','value_payment_ex');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                $where = make_array($params, $needle);
                if(is_numeric($params ['service_id']) AND $params ['service_id']>0){
                    $result = $this->CI->ConfigPaymentModel->check_service_id($params ['service_id']);
                    if($result==false){
                        $result = $this->CI->ConfigPaymentModel->saveItemconfig_game($where);
                        if ($result) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                        }
                    }else{
                        $result = $this->CI->ConfigPaymentModel->updateItemconfig_game($where);
                        if ($result) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                        }
                    }

                }else{
                    $this->_response = new MEAPI_Response_APIResponse($request, 'EXIT_SERVICE_ID');

                }


            } else {

                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    /*
     * API GET ITEM CONFIG PAYMENT
     * param  app(number)
     * param  connection_id(number)
     * param  key_payment{_nodepaysms,_nodepaycard,_nodepaybank}
     * param  key_value_payment{
     *                        _nodepaysms{key(mumber)},
     *                        _nodepaycard{key(card/string)},
     *                        _nodepaybank{key(code bank)}
     *                     }

     */
    public function get_keyvalue_payment(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;



            $needle = array('app','connection_id','key_payment','key_value');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $Json_methodPayment =   json_decode($objConfigPay['key_payment'],true);
                $Json_value_payment =   json_decode($objConfigPay['value_payment'],true);
                $dataItem   =   ($Json_value_payment[$params ['key_payment']][$params ['key_value']]);

                if ($dataItem) {
                    $dataItem['key_value']=$params ['key_value'];
                    $dataItem['key_payment']=$params ['key_payment'];
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $dataItem);
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




    /*
  * API ADD ITEM CONFIG PAYMENT
  * param  key_payment{_nodepaysms,_nodepaycard,_nodepaybank}
  * param  key_value_payment{
  *                        {key(mumber)},
  *                        {key(card/string)},
  *                        {key(code bank)}
  *                     }

  */
    public function add_keyvalue_payment(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;


            $params ['value_payment']=json_decode($params ['value_payment'],true);

            $needle = array('app','connection_id','key_payment','value_payment');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $Json_methodPayment =   json_decode($objConfigPay['key_payment'],true);
                $Json_value_payment =   json_decode($objConfigPay['value_payment'],true);
                $objValue   =   $Json_value_payment[$params ['key_payment']];
                if(is_array($params ['value_payment']) && $params ['value_payment']!=NULL){

                    if($params ['key_payment']=='_nodepaybank'){ echo $params ['value_payment']['money'];
                        $keySMS = ''.($params ['value_payment']['code']);
                        $arrPrice   =   array();
                        $view_ex   =   array();
                        if($params ['value_payment']['price']){
                            foreach($params ['value_payment']['price'] as $key=>$value){
                                $keyPrice = 'vn_'.($value/10000);
                                $arrPrice[$keyPrice] =   $value;
                                $view_ex[$keyPrice] = @$params ['value_payment']['view_ex'][$key];
                            }
                        }
                        $params ['value_payment']['price']=$arrPrice;
                        $params ['value_payment']['view_ex']=$view_ex;
                    }elseif($params ['key_payment']=='_nodepaysms'){
                        $keySMS = 'sms_'.($params ['value_payment']['money']/10000);
                    }elseif($params ['key_payment']=='_nodepaycard'){ echo $params ['value_payment']['money'];
                        $keySMS = ''.($params ['value_payment']['card']);
                    }
                    if(array_key_exists($keySMS,$objValue)){
                        $this->_response = new MEAPI_Response_APIResponse($request, 'EXIT_KEYPAYMENT_ITEM');
                        return;
                    }else
                        $objValue[$keySMS]=$params ['value_payment'];

                }

                $Json_value_payment[$params['key_payment']]=$objValue;

                $needleMake = array('app','connection_id','_value_payment');
                $where = make_array($params, $needleMake);
                $where ['_value_payment']=json_encode( $Json_value_payment);

                $result = $this->CI->ConfigPaymentModel->config_payment_update($where);
                if ($result) {
                    $this->CI->load->MEAPI_Library('ConfigPayment');
                    $result  =    $this->CI->ConfigPayment->update_config_payment_all();
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
    /*
 * API ADD ITEM CONFIG PAYMENT
 * param  key_payment{_nodepaysms,_nodepaycard,_nodepaybank}
 * param  key_value_payment{
 *                        {key(mumber)},
 *                        {key(card/string)},
 *                        {key(code bank)}
 *                     }

 */
    public function update_keyvalue_payment(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();

            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;

            $params ['value_payment']=json_decode($params ['value_payment'],true);


            $needle = array('app','connection_id','key_payment','value_payment');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('ConfigPayment');
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                if(is_array($params ['value_payment']) && $params ['value_payment']!=NULL) {
                    $objConfigPay = $this->CI->ConfigPaymentModel->getConfigPayment();
                    $Json_value_payment = json_decode($objConfigPay['value_payment'], true);
                    if (array_key_exists($params ['key_payment'], $Json_value_payment) && array_key_exists($params ['key_value'], $Json_value_payment[$params ['key_payment']])) {
                        if($params ['key_payment']=='_nodepaybank'){
                            $arrPrice   =   array();
                            $view_ex   =   array();
                            if($params ['value_payment']['price']){
                                foreach($params ['value_payment']['price'] as $key=>$value){
                                    $arrKey =   explode("_",$key);
                                    if($arrKey[0]=='vn'){
                                        $keyPrice   =$key;
                                    }else{
                                        $keyPrice = 'vn_'.($value/10000);
                                    }

                                    $arrPrice[$keyPrice] =   $value;
                                    $view_ex[$keyPrice] = @$params ['value_payment']['view_ex'][$key];
                                }
                            }
                            $params ['value_payment']['price']=$arrPrice;
                            $params ['value_payment']['view_ex']=$view_ex;

                        }

                        $Json_value_payment[$params ['key_payment']][$params ['key_value']] = $params ['value_payment'];

                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'NOT_EXIT_KEYPAYMENT_ITEM');
                        return;
                    }


                    $needleMake = array('_id_config','app','connection_id','_value_payment');
                    $where = make_array($params, $needleMake);
                    $where ['_value_payment']=json_encode( $Json_value_payment);

                    $result = $this->CI->ConfigPaymentModel->config_payment_update($where);

                    if ($result) {
                        $this->CI->load->MEAPI_Library('ConfigPayment');
                        $result  =    $this->CI->ConfigPayment->update_config_payment_all();
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                    }
                }else{
                    $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                    return;
                }
            } else {

                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }



    /*
    * API DELETE ITEM CONFIG PAYMENT
    * param  _id_config(number)
    * param  key_payment{_nodepaysms,_nodepaycard,_nodepaybank}
    * param  key_value_payment{
    *                        _nodepaysms{key(mumber)},
    *                        _nodepaycard{key(card/string)},
    *                        _nodepaybank{key(code bank)}
    *                     }

    */
    public function delete_keyvalue_payment(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();

        //  if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();

            $params ['app']=SERVICE_ID;
            $params ['connection_id']=CONNECTION_ID;


            $needle = array('app','connection_id','key_payment','key_value');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');
                $objConfigPay   =   $this->CI->ConfigPaymentModel->getConfigPayment();
                $Json_methodPayment =   json_decode($objConfigPay['key_payment'],true);
                $Json_value_payment =   json_decode($objConfigPay['value_payment'],true);

                if(array_key_exists($params ['key_payment'],$Json_value_payment) && array_key_exists($params ['key_value'],$Json_value_payment[$params ['key_payment']])){
                    if( $params ['key_price']!=NULL  && $params ['key_payment']=='_nodepaybank') {
                        if (array_key_exists($params ['key_price'], $Json_value_payment[$params ['key_payment']][$params ['key_value']]['price'])){
                            unset($Json_value_payment[$params ['key_payment']][$params ['key_value']]['price'][$params ['key_price']]);
                            unset($Json_value_payment[$params ['key_payment']][$params ['key_value']]['view_ex'][$params ['key_price']]);

                        }else
                            $this->_response = new MEAPI_Response_APIResponse($request, 'NOT_EXIT_KEYPAYMENT_ITEM');
                        return;


                    }else{
                        unset($Json_value_payment[$params ['key_payment']][$params ['key_value']]);
                    }
                }else{
                    $this->_response = new MEAPI_Response_APIResponse($request, 'NOT_EXIT_KEYPAYMENT_ITEM');
                    return;
                }
                $needleMake = array('_id_config','app','connection_id','_value_payment');
                $where = make_array($params, $needleMake);
                $where ['_value_payment']=json_encode( $Json_value_payment);

                $result = $this->CI->ConfigPaymentModel->config_payment_update($where);
                if ($result) {
                    $this->CI->load->MEAPI_Library('ConfigPayment');
                    $result  =    $this->CI->ConfigPayment->update_config_payment_all();
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

    public function update_config_payment_default(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $params ['connection_id']='0';
            $params ['service_id']='0';
            $needle = array('service_id', 'connection_id', 'method_payment', 'value_payment','method_payment_me','value_payment_me','method_payment_ex','value_payment_ex');
            if (is_required($params, $needle) == TRUE) {
                $where = make_array($params, $needle);
                $this->CI->load->MEAPI_Model('ConfigPaymentModel');

                $result = $this->CI->ConfigPaymentModel->updateItemconfig_game($where);
                //$result = $this->CI->ConfigPaymentModel->updateItemconfig_game($where);
                if ($result) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                }
            }else {

                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        }else {
            $this->_response = $authorize->getResponse();
        }

    }
	
	public function deleteCachePayment(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $params ['connection_id']='0';
            $params ['service_id']='0';
            $needle = array('service_id', 'connection_id', 'app');
          if (is_required($params, $needle) == TRUE) {
                $cache = $this->CI->cache->load('memcache', 'system_info');
                 $key_group = 'PAYEMENT_GROUP_ID_'.CONNECTION_ID;
                 $cache->delete_group($key_group);
                 $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS');
           }else {
               $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
               return;
           }

        }else {
            $this->_response = $authorize->getResponse();
        }

    }

    public function deleteCacheMopay(\MEAPI_RequestInterface $request){
        $authorize = new MEAPI_Controller_AuthorizeController();
        if($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('app');
            if (is_required($params, $needle) == TRUE) {
                $cache = $this->CI->cache->load('memcache', 'system_info');
                $key_group = 'PAYMENT_GROUP_ID_'.CONNECTION_ID;
                $cache->delete_group($key_group);
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS',$key_group);
            }else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
        }else {
            $this->_response = $authorize->getResponse();
        }

    }
    public function get_paymentdata(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();

            $this->CI->load->MEAPI_Model('InsideModel');

            $query = !empty($params['query'])?json_decode($params['query'],true) : array();

            $result['rows'] = $this->CI->InsideModel->get_paymentdata($query,array());

            if (empty($result) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_cron_payment(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {

            $params = $request->input_request();

            $this->CI->load->MEAPI_Model('InsideModel');

            $date_create = date('Y-m-d', strtotime("-1 days", time()));
            $run = 0;


            for ($i = 1; $i <= 30; $i++) {
                $date_create = date('Y-m-d', strtotime("-$i days", time()));
                //1. check data on days
                $where = array('createDate' => $date_create);
                $table = 'report_daily_payment';
                $data = $this->CI->InsideModel->get_where($where,array(), $table);
                if (empty($data) == TRUE) {
                    $run = 1;
                    break;
                }
            }
            if ($run) {

                $from = $date_create . '00:00:00';
                $to = $date_create . ' 23:59:59';

                $where = array("telco"=>array("viettel","vms","vina"),"datetime_create >="=>$from,"datetime_create <="=>$to  );
                $group = array(
                    'service_id','date(`datetime_create`)'
                );
                $query = array("service_id as game,sum(money) as money,date(`datetime_create`) as createDate");
                $table = 'payment_transaction';

                $result = $this->CI->InsideModel->getGroup($where,$query, $group , $table);

                if (empty($result) == FALSE) {
                    foreach ($result as $key => $value) {
                        $insert[] = array(
                            'money' => $value['money'],
                            'game' => $value['game'],
                            'credit' => $value['credit'],
                            'createDate' => $value['createDate']
                        );
                    }
                }



                if (empty($insert) == FALSE) {
                    $insert_result = $this->CI->InsideModel->insert_batch($insert, "report_daily_payment");
                    $this->_response = new MEAPI_Response_APIResponse($request, 'GET_SUCCESS');
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'GET_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'GET_FAIL');
            }

        } else {
            $this->_response = $authorize->getResponse();
        }
    }



}
