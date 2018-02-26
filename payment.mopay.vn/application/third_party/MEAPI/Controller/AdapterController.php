<?php

class MEAPI_Controller_AdapterController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_AdapterInterface
{

    public function get_payment_type(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $needle = array('access_token', 'info', 'channel', 'user_agent', 'platform', 'telco', 'lang', 'version');
            $params = $request->input_request();
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
            $access_token = $this->CI->MoboGraphAPI->verify_access_token($params);
            if ($params['dev'])
                $access_token['status'] = TRUE;
            if (empty($access_token['status']) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                return;
            } else {
                $template = MEAPI_Config_PaymentTemplate::payment_type($access_token['mobo_id']);
                if ($params['callback'] && $template) {
                    $result = array(
                        'code' => 110,
                        'desc' => 'REQUEST_SUCCESS',
                        'data' => $template
                    );
                    echo $params['callback'] . '(' . json_encode($result) . ')';
                    die;
                    return;
                }
                /*
                 * check mopay enable
                 * 1. check msv configs first
                 * 2. if msv config is not exists, base on app payment detail
                 */
                $this->CI->load->MEAPI_Helper('common');
                if ( $params['app'] == 154 || $params['app'] > 155 ) {
                    $this->CI->load->MEAPI_Model('ServiceModel');
                    $msv_explore = msv_explode($params['channel']);
                    $msv = $msv_explore['msv_base'];
                    $where = array(
                        'app' => $params['app'],
                        'msv' => $msv,
                        'platform' => $params['platform']
                    );
                    $msv_config = $this->CI->ServiceModel->get_msv_config($where);
                    if(!$msv_config){
                        $where = array(
                            'app' => $params['app']
                        );
                        $payment_detail = $this->CI->ServiceModel->get_info($where);
                        if($payment_detail && $payment_detail['mopay'] == 0){
                            unset($template['data']);
                        }
                    }else{
                        if($msv_config['mopay'] == 0){
                            unset($template['data']);
                        }
                    }
                }

                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $template);
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_payment_list(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $needle = array('access_token', 'info', 'channel', 'user_agent', 'platform', 'telco', 'lang', 'version');
            $params = $request->input_request();
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
            $access_token = $this->CI->MoboGraphAPI->verify_access_token($params);
            if ($params['dev'])
                $access_token['status'] = TRUE;
            if (empty($access_token['status']) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                return;
            } else {
                if (empty($access_token['detail']['active']) == TRUE) {
                    /*
                     * Tắt check chưa active không cho vào danh sách nạp tiền
                      $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_ACTIVE');
                      return;
                     */
                }
            }
            if ($params['target'] == 'wallet') {
                $target = 'wallet';
            } else {
                $target = 'mopay';
            }
            //define('API_VERSION', 3);            
            //$ss = json_encode(MEAPI_Config_PaymentTemplate::payment_list_v3(TRUE, $params['language'], $target));
            //$template = 'payment_list('.$ss.')';
            $template = MEAPI_Config_PaymentTemplate::payment_list(TRUE, $params, $target);
            //echo $template;die;
            $template['app_name'] = (APP_NAME == '141' ? 'Cào rùa 3K' : APP_NAME);
            $template['mobo_id'] = $access_token['mobo_id'];
            if ($params['callback'] && $template) {
                $result = array(
                    'code' => 110,
                    'desc' => 'REQUEST_SUCCESS',
                    'data' => $template
                );
                echo $params['callback'] . '(' . json_encode($result) . ')';
                die;
                return;
            }

            if ($params['platform'] == 'web') {
                unset($template['sms']);
            }

            if (APP_NAME == '141') {
                unset($template['sms'], $template['card'], $template['banking'], $template['wallet']);
            }

            // @vietbl: con game heroes - 154 e remove hình thức sms ở mopay wap giúp nha em
            // 2017-02-23
            // payment list support
            if(APP_NAME >= 154){
                unset($template['sms']);
            }

            if (empty($template) == FALSE) {
                MEAPI_Log::writeCsv(merge_log('REQUEST', array($params, $template)), 'REQUEST_SUCCESS');
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $template);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_payment_exchange(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $needle = array('access_token', 'info', 'channel', 'user_agent', 'platform', 'telco', 'lang', 'version');
            $params = $request->input_request();
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
            $access_token = $this->CI->MoboGraphAPI->verify_access_token($params);
            if ($params['dev'])
                $access_token['status'] = TRUE;
            if (empty($access_token['status']) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                return;
            } else {
                $template = MEAPI_Config_PaymentTemplate::exchange_rate();
                if ($params['callback'] && $template) {
                    $result = array(
                        'code' => 110,
                        'desc' => 'REQUEST_SUCCESS',
                        'data' => $template
                    );
                    echo $params['callback'] . '(' . json_encode($result) . ')';
                    die;
                    return;
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $template);
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function verify_card(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            //$request->input_request['language'] = $params['lang'];
            //$request->input_request['ip'] = $params['ip_user'];
            //var_dump($request);die;
            $needle = array('serial', 'pin', 'telco', 'ip', 'lang', 'user_agent', 'platform', 'access_token');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));

                if ($params['dev'])
                    $account_info = array('mobo_id' => '128147013');
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $a = $this->CI->MDeposit->verify_card($request, $account_info);
                    $response = $a->getArray();

                    //var_dump($a->getArray());die;
                    //var_dump(json_decode($this->_response,TRUE));
                    if ($params['callback'] && $response && $params['success']) {
                        $result = array(
                            'code' => 110,
                            'desc' => 'REQUEST_SUCESS',
                            'data' => array('message' => 'nạp thành công', 'credit' => 10, 'unit' => 'vnđ', 'money' => 10000, 'balance' => 10000)
                        );
                        echo $params['callback'] . '(' . json_encode($result) . ')';
                        die;
                        return;
                    } elseif ($params['callback'] && $response) {
                        $result = array(
                            'code' => $response['code'],
                            'desc' => $response['desc'],
                            'data' => $response['data']
                        );
                        echo $params['callback'] . '(' . json_encode($result) . ')';
                        die;
                        return;
                    }
                    if ($response['code'] == 110 && $response['data']['service_data'] && $params['url_callback']) {
                        //redirect to MEM success
                        $response['data']['redirect'] = $params['url_callback'] . '?status=1&service_data=' . $response['data']['service_data'];
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response['data']);
                    } elseif ($response['code'] == 111 && $response['data']['error_code'] == '-6' && $params['url_callback']) {
                        //redirect to MEM error
                        $response['data']['redirect'] = $params['url_callback'] . '?status=2';
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $response['data']);
                    } elseif ($response['code'] == 110) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response['data']);
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', $response['data']);
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

    public function get_items(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        $this->CI->load->MEAPI_Helper('common');
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'channel', 'user_agent', 'platform', 'lang');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            //$params['language'] = $params['lang'];
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $response['options'] = array(
                        'description' => 'Vui lòng chọn item',
                        'place_holder' => 'Chọn item'
                    );
                    $this->CI->load->MEAPI_Library('Mopay/MPayment', 'MPayment');
                    $params['service_id'] = SCOPE_ID;
                    $response['items'] = $this->CI->MPayment->get_items($params['service_id']);
                    /*foreach ($response['items'] as $key => $value){
                        if($value['local_only'] && !check_ip_local()){
                            unset($response['items'][$key]);
                        }
                        unset($response['items'][$key]['local_only']);
                    }*/
                    unset($response['items'][$key]['local_only']);
                    if (SCOPE_ID == '106' || SCOPE_ID == '101') {
                        array_push($response['items'], array(
                            'itemId' => 'item-1a',
                            'itemName' => 'Gói 1a',
                            'credit' => '10'
                        )); // $response
                        array_push($response['items'], array(
                            'itemId' => 'goi3',
                            'itemName' => 'Gói 3',
                            'credit' => '30'
                        )); // $response
                        array_push($response['items'], array(
                            'itemId' => 'item-2a',
                            'itemName' => 'Gói 2a',
                            'credit' => '30'
                        )); // $response
                    }


                    if ($params['callback'] && $response) {
                        $result = array(
                            'code' => 110,
                            'desc' => 'REQUEST_SUCCESS',
                            'data' => $response
                        );
                        echo $params['callback'] . '(' . json_encode($result) . ')';
                        die;
                        return;
                    }
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
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

    public function purchase_item(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'lang', 'user_agent', 'platform', 'access_token', 'item_id', 'info');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $this->CI->load->MEAPI_Library('Language');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MPayment', 'MPayment');
                    $credit = $this->CI->MPayment->item_info($params['item_id']);
                    //echo $params['item_id'];                    
                    if ($params['item_id'] === 'goi3' || $params['item_id'] === '123-aaa') {
                        $response = array(
                            'message' => '<i>Chúc mừng bạn</i><br /> Mua vật phẩm <b>Gói 3</b> thành <sub>công!</sub>', //$this->CI->Language->item('EXCHANGE_SUCCESS'),
                            'credit' => '30',
                            'unit' => 'vang',
                            'money' => '10000'
                        );
                        if ($params['callback']) {
                            $result = array(
                                'code' => 110,
                                'desc' => 'REQUEST_SUCCESS',
                                'data' => $response
                            );
                            echo $params['callback'] . '(' . json_encode($result) . ')';
                            die;
                            return;
                        }
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    } elseif ($credit) {
                        $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                        $output = $this->CI->MWithdraw->withdraw($request, $account_info, array('credit' => $credit));
                        /*
                         * do sthing
                         */
                        $result_withdraw = $output->getArray();
                        if ($result_withdraw['code'] == 110) {
                            if (!$params['withdraw']) {
                                if (empty($params['mobo_service_id']) === FALSE) {
                                    $mobo_service_id = $params['mobo_service_id'];
                                } else {
                                    $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                                    $mobo_service_id = $tmp_access_token['mobo_service_id'];
                                }
                                $this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
                                $arrPay = array(
                                    'mobo_id' => $account_info['mobo_id'],
                                    'money' => 0,
                                    'credit' => intval($credit),
                                    'payment_type' => 'wallet',
                                    'info' => $params['info'],
                                    'service_id' => $params['service_id'] ? $params['service_id'] : SCOPE_ID,
                                    'channel' => $params['channel'],
                                    'mobo_service_id' => $mobo_service_id,
                                    'platform' => $params['platform'],
                                    'language' => $params['language'],
                                    'receipt_data' => $result_withdraw['data']['receipt_data']
                                );
                                $pay = $this->CI->MoboService->pay_to_service($arrPay);
                                if (is_array($pay) === TRUE && $pay['credit'] > 0) {
                                    $response = array(
                                        'message' => $this->CI->Language->item('EXCHANGE_SUCCESS'),
                                        'credit' => intval($pay['mcoin']),
                                        'unit' => $pay['unit'],
                                        'money' => $pay['credit']
                                    );
                                    if ($params['callback']) {
                                        $result = array(
                                            'code' => 110,
                                            'desc' => 'REQUEST_SUCCESS',
                                            'data' => $response
                                        );
                                        echo $params['callback'] . '(' . json_encode($result) . ')';
                                        die;
                                        return;
                                    }

                                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                                    return;
                                } else {
                                    if ($params['callback']) {
                                        $result = array(
                                            'code' => 111,
                                            'desc' => 'REQUEST_FAIL',
                                            'data' => array('message' => 'Nạp vào game thất bại . Liên hệ 19006611 để biết thêm chi tiết')
                                        );
                                        echo $params['callback'] . '(' . json_encode($result) . ')';
                                        die;
                                        return;
                                    }
                                    MEAPI_Log::writeCsv($arrPay, 'EXCHANGE_CREDIT_FAIL');
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Nạp vào game thất bại . Liên hệ 19006611 để biết thêm chi tiết'));
                                    return;
                                }
                            }
                        }
                        if ($params['callback']) {
                            $response = $output->getArray();
                            $response['data']['message'] = substr($response['data']['message'], 13, strlen($response['data']['message']));
                            unset($response['message']);
                            /*$result = array(
                                'code' => 111,
                                'desc' => 'REQUEST_FAIL',
                                'data' => array('message' => 'Mua vật phẩm không thành công')
                            );*/
                            echo $params['callback'] . '(' . json_encode($response) . ')';
                            die;
                            return;
                        }
                        $this->_response = $output;
                    } else {
                        if ($params['callback']) {
                            $result = array(
                                'code' => 111,
                                'desc' => 'REQUEST_FAIL',
                                'data' => array('message' => 'Mua vật phẩm không thành công')
                            );
                            echo $params['callback'] . '(' . json_encode($result) . ')';
                            die;
                            return;
                        }
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Mua vật phẩm không thành công'));
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

    public function balance(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'channel', 'user_agent', 'platform', 'lang');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
                    $result = $this->CI->MWallet->balance($account_info['mobo_id']);
                    if ($result['code'] == 1) {
                        $this->CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
                        $cache = $this->CI->cache->load('memcache', 'mopay_info');

                        $partner_config = $cache->store('MOPAY_PARTNER_APP_EXCHANGE_' . SCOPE_ID, $this->CI->MecorpPayment, 'get_app_exchange', array(SCOPE_ID));
                        $response = $result['detail'];
                        //Fix lỗi sai chính tả trong SDK                        

                        $response['exchange'] = array(
                            'rate' => $partner_config['rate'] ? $partner_config['rate'] : 0, //percentage
                            'unit' => $partner_config['unit'] ? $partner_config['unit'] : 'Error'
                        );
                        $response['account'] = array(
                            'fullname' => $account_info['fullname'],
                            'avatar' => 'http://graph.mobo.vn/avatar/' . $account_info['mobo_id'],
                        );
                        $response['more'] = array(
                            'website' => 'http://mopay.vn/nap-mcoin.html?access_token=' . $params['access_token'],
                            'store' => '',
                            'package_name' => ''
                        );
                        $response['items'] = array(
                            array(
                                'itemId' => 'item.1',
                                'itemName' => 'Gói 1'
                            ),
                            array(
                                'itemId' => 'item.2',
                                'itemName' => 'Gói 2'
                            ),
                        );
                        if ($params['callback'] && $response) {
                            $result = array(
                                'code' => 110,
                                'desc' => 'REQUEST_SUCCESS',
                                'data' => $response
                            );
                            echo $params['callback'] . '(' . json_encode($result) . ')';
                            die;
                            return;
                        }
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
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

    public function get_link_banking(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('money', 'bank_type', 'bank_code', 'ip', 'lang', 'user_agent', 'platform', 'access_token');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if ($params['ip_user']) {
                $params['ip'] = $params['ip_user'];
            }
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('BankGateway/Bank_mecorp', 'bank_gateway');
                    $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                    $info = array(
                        'language' => $params['language'],
                        'ip' => $params['ip'],
                        'user_agent' => $params['user_agent'],
                        'platform' => $params['platform'],
                        'service_id' => SCOPE_ID,
                        'channel' => $params['channel'],
                        'mobo_service_id' => $tmp_access_token['mobo_service_id'],
                        'info' => $params['info'],
                        'callback' => $params['callback'],
                        'postback' => $params['postback'],
                    );
                    if (empty($params['direct']) === FALSE) {
                        $info['direct'] = 1;
                    }
                    if (empty($params['info']) === FALSE) {
                        $tmp = json_decode($params['info'], TRUE);
                        if (is_array($tmp) === TRUE) {
                            $info = array_merge($tmp, $info);
                        }
                    }
                    $info = json_encode($info);
                    $arrBanking = array(
                        'banking_transaction' => strtoupper('b' . dechex($account_info['mobo_id'] . rand(1111111, 9999999))),
                        'mobo_id' => $account_info['mobo_id'],
                        'money' => $params['money'],
                        'bank_type' => $params['bank_type'],
                        'data' => $info,
                        'bank_code' => $params['bank_code']
                    );
                    $this->CI->bank_gateway->set_params($arrBanking);
                    if ($params['bank_code'] == 'visa') {
                        if ($params['money'] < 500000 && is_mecorp() === FALSE) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'BANKING_LIMIT');
                            return;
                        }
                        $result = $this->CI->bank_gateway->get_link(TRUE);
                    } else {
                        $result = $this->CI->bank_gateway->get_link();
                    }

                    $response = array(
                        'link' => $result['url']
                    );
                    if (empty($response['link']) === FALSE) {
                        if ($params['callbacks'] && $response) {
                            $result = array(
                                'code' => 110,
                                'desc' => 'REQUEST_SUCCESS',
                                'data' => $response
                            );
                            echo $params['callback'] . '(' . json_encode($result) . ')';
                            die;
                            return;
                        }
                        $response['link'] = $params['package_name'] . '://action=open_browser_outsite&url=' . urlencode($response['link']);
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        return;
                    }
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                } else {
                    if ($params['callbacks']) {
                        $result = array(
                            'code' => 111,
                            'desc' => 'REQUEST_FAIL',
                            'data' => array()
                        );
                        echo $params['callback'] . '(' . json_encode($result) . ')';
                        die;
                        return;
                    }
                    $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_sms_content(MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('telco', 'ip', 'lang', 'user_agent', 'platform', 'service_number', 'access_token', 'channel');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if ($params['ip_user']) {
                $params['ip'] = $params['ip_user'];
            }
            if (is_required($params, $needle) == TRUE) {
                if (strpos($params['service_number'], '@') > 0) {
                    $tmp_service = explode('@', $params['service_number']);
                    $params['service_money'] = $tmp_service[0];
                    $params['service_number'] = $tmp_service[1];
                    $arrMoneyToDetail = array(
                        '10000' => array('subcode' => 'NAP10', 'credit' => 50),
                        '20000' => array('subcode' => 'NAP20', 'credit' => 100),
                        '50000' => array('subcode' => 'NAP50', 'credit' => 250),
                        '100000' => array('subcode' => 'NAP100', 'credit' => 500),
                    );
                    $params['service_credit'] = $arrMoneyToDetail[$tmp_service[0]]['credit'];
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                    return;
                }
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                $direct = 0;
                if (empty($params['direct']) === FALSE) {
                    $direct = 1;
                }
                if (is_array($account_info)) {
                    $tmp_access_token = json_decode(base64_decode($params['access_token']), TRUE);
                    $sms_transaction_id = dechex(rand(1111, 9999999));
                    $arrInsert = array(
                        'language' => $params['language'],
                        'user_agent' => $params['user_agent'],
                        'platform' => $params['platform'],
                        'sms_transaction_id' => $sms_transaction_id,
                        'mobo_id' => $account_info['mobo_id'],
                        'service_number' => $params['service_number'],
                        'scope_id' => SCOPE_ID,
                        'ip' => $params['ip'],
                        'direct' => $direct,
                        'data' => $params['info'],
                        'channel' => $params['channel'],
                        'mobo_service_id' => $tmp_access_token['mobo_service_id']
                    );
                    $this->CI->load->MEAPI_Model('PaymentModel');
                    $result = $this->CI->PaymentModel->insert_sms_transaction($arrInsert);
                    if (is_array($result) && $result['code'] == 1) {

                        if (empty($direct) === FALSE) {
                            $this->CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
                            $cache = $this->CI->cache->load('memcache', 'mopay_info');
                            $partner_config = $cache->store('MOPAY_PARTNER_APP_EXCHANGE_' . SCOPE_ID, $this->CI->MecorpPayment, 'get_app_exchange', array(SCOPE_ID));
                            if (empty($partner_config) === FALSE) {
                                $rate = $partner_config['rate'];
                                $unit = $partner_config['unit'];
                            } else {
                                $rate = 0;
                                $unit = 'Error';
                            }
                        } else {
                            $rate = 1;
                            $unit = 'mcoin';
                        }

                        if ($params['service_number'] = '9029') {
                            $response = array(
                                'content' => 'ME MOPAY ' . $arrMoneyToDetail[$params['service_money']]['subcode'] . ' ' . SCOPE_ID . ' ' . $account_info['mobo_id'] . ' ' . $sms_transaction_id,
                                'phone' => $params['service_number'],
                                'message' => 'Bạn có muốn mua ' . $params['service_credit'] * $rate . ' ' . $unit . ' ? '
                            );
                            if ($params['callback']) {
                                $result = array(
                                    'code' => 110,
                                    'desc' => 'REQUEST_SUCCESS',
                                    'data' => $response
                                );
                                echo $params['callback'] . '(' . json_encode($result) . ')';
                                die;
                                return;
                            }
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                            return;
                        } else {

                        }
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
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

    /**
     * @params: transaction_id, order_id
     * @response: api response
     */
    public function verify_momo(\MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('transaction_id', 'order_id', 'channel', 'ip', 'lang', 'user_agent', 'platform', 'device_id', 'info', 'tracking_info');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                $this->CI->load->MEAPI_Helper('common');
                if(check_ip_local()){
                    $access_token = json_decode(base64_decode($params['access_token']),true);
                    $account_info = array(
                        'mobo_id' => $access_token['mobo_id']
                    );
                }
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $this->_response = $this->CI->MDeposit->verify_momo($request, $account_info);
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

    /**
     * sample test api momo
     * @params: transaction_id, order_id
     * @response: api response
     */

    public function test_momo(\MEAPI_RequestInterface $request)
    {
        $params = $request->input_request();
        $needle = array('transaction_id', 'order_id');
        if (is_required($params, $needle) == TRUE) {
            $this->CI->load->MEAPI_Library('Momo/MomoService', 'MomoService');
            $this->CI->MomoService->set_params($params);
            $response = $this->CI->MomoService->verify();
            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
        } else {
            $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
        }
    }

    /**
     * @params: transaction_id, order_id
     * @response: api response
     */
    public function add_money_mpay(\MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
//            $needle = array('mobo_id', 'transaction_id', 'order_id', 'channel', 'ip', 'lang', 'user_agent', 'platform', 'device_id', 'info', 'tracking_info', 'pay_type', 'pay_subtype', 'money');
            $needle = array('access_token', 'transaction_id', 'order_id', 'channel', 'ip', 'lang', 'user_agent', 'platform', 'device_id', 'info', 'direct', 'tracking_info', 'money');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if (is_required($params, $needle) == TRUE) {
                $access_token = json_decode(base64_decode($params['access_token']),true);
                $account_info = array(
                    'mobo_id' => $access_token['mobo_id']
                );
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $this->_response = $this->CI->MDeposit->verify_mpay($request, $account_info);
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


    /**
     * @params: transaction_id, order_id
     * @response: api response
     */
    public function add_money_8595(\MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('transaction_id', 'order_id', 'telco', 'channel', 'ip', 'lang', 'user_agent', 'platform', 'device_id', 'info', 'tracking_info', 'money');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if (is_required($params, $needle) == TRUE) {
                $access_token = json_decode(base64_decode($params['access_token']),true);
                $account_info = array(
                    'mobo_id' => $access_token['mobo_id']
                );
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $this->_response = $this->CI->MDeposit->verify_8595($request, $account_info);
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


    /**
     * @params: transaction_id, order_id
     * @response: api response
     */
    public function add_money_webmoney(\MEAPI_RequestInterface $request)
    {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeTrue($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'transaction_id', 'order_id', 'channel', 'ip', 'lang', 'user_agent', 'platform', 'device_id', 'info', 'direct', 'tracking_info', 'money');
            if ($params['lang']) {
                $params['language'] = $params['lang'];
            }
            if (is_required($params, $needle) == TRUE) {
                $access_token = json_decode(base64_decode($params['access_token']),true);
                $account_info = array(
                    'mobo_id' => $access_token['mobo_id']
                );
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $this->_response = $this->CI->MDeposit->verify_webmoney($request, $account_info);
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
