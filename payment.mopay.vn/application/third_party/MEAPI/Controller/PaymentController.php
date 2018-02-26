<?php

class MEAPI_Controller_PaymentController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_PaymentInterface {

    public function verify_card(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('serial', 'pin', 'telco', 'ip', 'language', 'user_agent', 'platform', 'access_token');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
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

    public function verify_card_direct(MEAPI_RequestInterface $request) {
        //$authorize = new MEAPI_Controller_AuthorizeController();
        //if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            /*
             * http://local.payment.mopay.vn?control=payment&func=verify_card_direct&mobo_id=128147013&serial=36258200403879&pin=17557454377113&card=vina&channel=1%7Cme%7C1.0.6%7CEnt%7Cmsv_23_file&ip=192.168.0.102&platform=ios&telco=vina&user_agent=ios&version=1.0.6&device_id=4ffaa156bbf3da2882960af276ff0d68681d736e&language=vi&direct=1&sdk_vesion=2.3.0.10.20160127&appsflyer_info=%7B%22idfa%22%3A%22C1872F30-8595-494C-A681-47899429B989%22%2C+%22appsflyer_id%22%3A%221499705196000-3132419%22%7D&access_token=eyJpZGVudGlmeSI6IjUzNzI5MjEyODE0NzAxMyIsIm1vYm9faWQiOiIxMjgxNDcwMTMiLCJtb2JvX3NlcnZpY2VfaWQiOiIxNTUxNTQ5NDIxNzI1OTM5NDAyIiwiaXNfZmlyc3QiOmZhbHNlLCJoYXNoIjoiYzI5NmU0MzY3NGYyOWJhZTRkMTgzODQyNWI0ZjA3ZGYifQ%3D%3D&transaction_id=901547659&order_id=155YJueRVWF&channel=2%7Cme%7C1.0%7CAppstore%7Cmsv_8_store&platform=ios&user_agent=ios&ip=118.69.76.212&lang=vi&device_id=0f722db068e63fe247cba1c7090bca06b82c1428&tracking_info=%7B%22pixel%22%3A%7B%22track_id%22%3A%22526452C3-A7FE-4AAA-AC13-5A6361B1E0F8%22%2C%22device_id%22%3A%220f722db068e63fe247cba1c7090bca06b82c1428%22%7D%2C%22appsflyer_id%22%3A%221480753536000-6505608%22%2C%22ios_ifa%22%3A%2273A2D66E-2683-4904-A29C-D757ABF3D487%22%2C%22ios_ifv%22%3A%22526452C3-A7FE-4AAA-AC13-5A6361B1E0F8%22%7D&info=%7B%0A++%22character_name%22+%3A+%22SauNghia%22%2C%0A++%22character_id%22+%3A+%22100000689%22%2C%0A++%22server_id%22+%3A+%220%22%2C%0A++%22card_type%22+%3A+%220%22%0A%7D&direct=1&app=155&token=0d86e223833b5c9dfd5cbe0452ff0408
             */
            $needle = array('serial', 'pin', 'telco', 'ip', 'language', 'user_agent', 'platform', 'mobo_id','access_token');

            if (is_required($params, $needle) == TRUE) {
                $account_info = array(
                    'mobo_id' => $params['mobo_id']
                );

                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $this->_response = $this->CI->MDeposit->verify_card($request, $account_info);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        //} else {
        //    $this->_response = $authorize->getResponse();
        //}
    }

    public function verify_inapp_apple(MEAPI_RequestInterface $request) {
        //TODO: Implement verify_inapp_apple() method.
    }

    public function verify_inapp_google(MEAPI_RequestInterface $request) {
        //TODO: Implement verify_inapp_google() method.
    }

    public function get_payment_list(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE OR isset($_GET['dev'])) {
            $params = $request->input_request();
            $needle = array('access_token', 'info', 'channel', 'user_agent', 'platform', 'telco', 'language');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
            $access_token = $this->CI->MoboGraphAPI->verify_access_token($params);

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
            $template = MEAPI_Config_PaymentTemplate::payment_list(TRUE, $params['language'], $target);
            if ($params['platform'] == 'web') {
                unset($template['sms']);
            }
            if (empty($template) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $template);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_sms_content(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('telco', 'ip', 'language', 'user_agent', 'platform', 'service_number', 'access_token', 'channel');
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
                                'message' => 'Bạn có muốn mua ' . intval($params['service_credit'] * $rate) . ' ' . $unit . ' ? '
                            );
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

    public function exchange_credit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform', 'access_token', 'credit', 'info');
            if (is_required($params, $needle) == TRUE) {
				$this->CI->load->helper('utils');				
                if ($params['credit'] < 200 && !is_mecorp()) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Bạn phải chuyển tối thiểu 200 mCoin'));
                    return;
                }
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $this->CI->load->MEAPI_Library('Language');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $output = $this->CI->MWithdraw->withdraw($request, $account_info);
                    $result_withdraw = $output->getArray();
                    if ($result_withdraw['code'] == 110) {
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
                            'credit' => $params['credit'],
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
                                'credit' => intval($pay['credit']),
                                'unit' => $pay['unit'],
                                'money' => $pay['money']
                            );
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                            return;
                        } else {
                            MEAPI_Log::writeCsv($arrPay, 'EXCHANGE_CREDIT_FAIL');
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Nạp vào game thất bại . Liên hệ 19006611 để biết thêm chi tiết'));
                            return;
                        }
                    }
                    $this->_response = $output;
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

    public function purchase_item(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform', 'access_token', 'item_id', 'info');
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
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                    } elseif ($credit) {
                        $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                        $output = $this->CI->MWithdraw->withdraw($request, $account_info, array('credit' => $credit));
                        /*
                         * do sthing
                         */
                        $result_withdraw = $output->getArray();
                        if ($result_withdraw['code'] == 110) {
                            if(!$params['withdraw']) {
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
                                    'credit' => $params['credit'],
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
                                        'credit' => intval($pay['credit']),
                                        'unit' => $pay['unit'],
                                        'money' => $pay['money']
                                    );
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                                    return;
                                } else {
                                    MEAPI_Log::writeCsv($arrPay, 'EXCHANGE_CREDIT_FAIL');
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL', array('message' => 'Nạp vào game thất bại . Liên hệ 19006611 để biết thêm chi tiết'));
                                    return;
                                }
                            }
                        }
                        $this->_response = $output;
                    } else {
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

    public function rate_to_service(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform', 'service_id', 'credit');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
                $cache = $this->CI->cache->load('memcache', 'mopay_info');

                $partner_config = $cache->store('MOPAY_PARTNER_APP_EXCHANGE_' . $params['service_id'], $this->CI->MecorpPayment, 'get_app_exchange', array($params['service_id']));
                $response = array(
                    'rate' => $partner_config['rate'] ? $partner_config['rate'] : 0, //percentage
                    'unit' => $partner_config['unit'] ? $partner_config['unit'] : 'Error'
                );
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
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
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $this->_response = $this->CI->MWithdraw->withdraw($request, $account_info);
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

    public function verify_withdraw_transaction(MEAPI_RequestInterface $request) {
        $params = $request->input_request();
        $needle = array('receipt_data', 'result_string', 'service_id');
        if (is_required($params, $needle) == TRUE) {
            $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
            $this->_response = $this->CI->MWithdraw->verify_transaction($request);
        } else {
            $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
        }
    }

    public function get_link_banking(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('money', 'bank_type', 'bank_code', 'ip', 'language', 'user_agent', 'platform', 'access_token');
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
                        'tracking_info' => $params['tracking_info'],
                        'device_id' => $params['device_id']
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
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                        return;
                    }
                    $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_FAIL');
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

    public function get_payment_exchange(MEAPI_RequestInterface $request) {
        $params = $request->input_request();		
        if (empty($params['is_mopay']) === TRUE) {
			unset($params['language']);
            $this->CI->load->MEAPI_library('curl');
            $url = 'http://service.mobo.vn/?' . http_build_query($params);
            $response = $this->CI->curl->get($url);
            @header('Content-type: application/json');
            echo $response;
            exit;
        }

        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform');
            if (is_required($params, $needle) == TRUE) {

                $arrGroupType = MEAPI_Config_PaymentTemplate::exchange_group();
                $arrExchange = MEAPI_Config_PaymentTemplate::exchange_rate();

                if (empty($params['type']) === FALSE) {
                    if (empty($arrGroupType[$params['type']]) === FALSE) {
                        $response = array();
                        foreach ($arrGroupType[$params['type']] as $k => $v) {
                            $response[] = $arrExchange[$v];
                        }
                    } else {
                        $response[] = $arrExchange[$params['type']];
                    }
                } else {
                    foreach ($arrExchange as $k => $v) {
                        $response[] = $arrExchange[$k];
                    }
                }

                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                return;
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_banking_price(MeAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('ip', 'language', 'user_agent', 'platform', 'bank_code');
            if (is_required($params, $needle) == TRUE) {

                $arrExchange = MEAPI_Config_PaymentTemplate::exchange_rate();
                $data = $arrExchange['banking-atm'];
                $response = $data['items'];
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $response);
                return;
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
            $needle = array('access_token', 'channel', 'user_agent', 'platform', 'language');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                
				if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MWallet', 'MWallet');
                    $result = $this->CI->MWallet->balance($account_info['mobo_id']);
					
                    if ($result['code'] == 1) {
                        $this->CI->load->MEAPI_Library('Mecorp/MecorpPayment', 'MecorpPayment');
                        $cache = $this->CI->cache->load('memcache', 'mopay_info');

                        //$partner_config = $cache->store('MOPAY_PARTNER_APP_EXCHANGE_' . SCOPE_ID, $this->CI->MecorpPayment, 'get_app_exchange', array(SCOPE_ID));
                        $partner_config = $this->CI->MecorpPayment->get_app_exchange(SCOPE_ID);
						$response = $result['detail'];
						
                        //Fix lỗi sai chính tả trong SDK
                        $params['information'] = $params['infomation'];
                        if (empty($params['information']) === FALSE) {

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

    public function get_items(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'channel', 'user_agent', 'platform', 'language');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $response['options'] = array(
                        'description' => 'Vui lòng chọn item',
                        'place_holder' => 'Chọn item'
                    );
                    $this->CI->load->MEAPI_Library('Mopay/MPayment', 'MPayment');
                    $response['items'] = $this->CI->MPayment->get_items(SERVICE_ID);
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

    public function receive_banking_success(MEAPI_RequestInterface $request) {
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('BankGateway/Bank_mecorp', 'bank_gateway');
        if ($this->CI->bank_gateway->validate_data(1, $params['data'], $params['token']) == TRUE) {
            $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
            $tmp = json_decode(base64_decode($params['data']), TRUE);
            $banking_info = array(
                'banking_transaction' => $tmp['partner_trans_id'],
                'mobo_id' => $tmp['username'],
                'bank_type' => $tmp['bank_type'],
                'bank_code' => $tmp['bank_code'],
                'info' => json_decode($tmp['data'], TRUE)
            );
            define('SCOPE_ID', $banking_info['info']['service_id']);
            $output = $this->CI->MDeposit->verify_banking($request, $banking_info);
            $arrOutput = $output->getArray();
            $tmp_data = json_decode($tmp['data'], TRUE);
            $callback = $tmp_data['callback'];
			$postback = $tmp_data['postback'];
			//echo '<pre>';
			//print_r($arrOutput);
			//die();
            if ($arrOutput['code'] == 110) {
                $data = array(
                    'money' => $arrOutput['data']['money'],
                    'credit' => $arrOutput['data']['credit'],
                    'balance' => $arrOutput['data']['balance'],
                    'message' => $arrOutput['data']['message'],
                    'unit' => $arrOutput['data']['unit'],
                    'language' => $banking_info['info']['language'],
                    'callback' => $callback,
                    'status' => TRUE
                );
            } else {
                $data = array(
                    'message' => $arrOutput['data']['message'],
                    'language' => $banking_info['info']['language'],
                    'callback' => $callback
                );
            }						
			if($postback && $callback){			
				$this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
				$a_postback = explode('-', $postback);
				$a_orderId = explode('.', $a_postback[1]);				
				$args = array(
					'money' => ($arrOutput['data']['money']?$arrOutput['data']['money']:0),
                    'credit' => ($arrOutput['data']['credit']?$arrOutput['data']['credit']:0),
                    'balance' => ($arrOutput['data']['balance']?$arrOutput['data']['balance']:0),
                    'message' => $arrOutput['data']['message'],
                    'unit' => $arrOutput['data']['unit'],
					'order_id' => $a_orderId[0]
				);			
				$postback_result = $this->CI->MoboService->_call_bank_postback($postback, $args);								
				redirect($callback);
				return;
			}
			
            $this->_response = new MEAPI_Response_HTMLResponse($request, $this->CI->load->view('MEAPI/template_banking', $data, TRUE));
            return;
        }
        $this->_response = new MEAPI_Response_HTMLResponse($request, $this->CI->load->view('MEAPI/template_banking', array('message' => 'Thông tin giao dịch không hợp lệ'), TRUE));
    }

    public function receive_banking_fail(MEAPI_RequestInterface $request) {
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('Language');
        $this->CI->load->MEAPI_Library('BankGateway/Bank_mecorp', 'bank_gateway');        
        $tmp = json_decode(base64_decode($params['data']), TRUE);
        $tmp_data = json_decode($tmp['data'], TRUE);
        $callback = $tmp_data['callback'];
		$postback = $tmp_data['postback'];
		//echo '<pre>';
		//print_r($postback);
		//die();
        if ($this->CI->bank_gateway->validate_data(0, $params['data'], $params['token']) == TRUE) {
			if($postback && $callback){			
				$this->CI->load->MEAPI_Library('Mobo/MoboService', 'MoboService');
				$a_postback = explode('-', $postback);
				$a_orderId = explode('.', $a_postback[1]);				
				$args = array(
					'money' => 0,
                    'credit' => 0,
                    'balance' => 0,
                    'message' => 'Giao dịch không thành công',
                    'unit' => 'vnd',
					'order_id' => $a_orderId[0]
				);			
				$postback_result = $this->CI->MoboService->_call_bank_postback($postback, $args);												
				redirect($callback);
				return;
			}
            $this->CI->Language->init($tmp_data['language']);
            $this->_response = new MEAPI_Response_HTMLResponse($request, $this->CI->load->view('MEAPI/template_banking', array('message' => $this->CI->Language->item('WEB_BANKING_FAIL', array('banking_transaction' => $tmp['partner_trans_id'])), 'callback' => $callback, 'language' => $tmp_data['language']), TRUE));
            return;
        }
        $this->_response = new MEAPI_Response_HTMLResponse($request, $this->CI->load->view('MEAPI/template_banking', array('message' => $this->CI->Language->item('WEB_BANKING_INVALID'), 'callback' => $callback, 'language' => $tmp_data['language']), TRUE));
    }

    public function history_deposit(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('telco', 'ip', 'language', 'user_agent', 'platform', 'access_token', 'offset', 'limit');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                    $this->_response = $this->CI->MDeposit->history($request, $account_info);
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

    public function history_withdraw(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('telco', 'ip', 'language', 'user_agent', 'platform', 'access_token', 'offset', 'limit');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mobo/MoboGraphAPI', 'MoboGraphAPI');
                $account_info = $this->CI->MoboGraphAPI->verify_access_token(array('access_token' => $params['access_token']));
                if (is_array($account_info)) {
                    $this->CI->load->MEAPI_Library('Mopay/MWithdraw', 'MWithdraw');
                    $this->_response = $this->CI->MWithdraw->history($request, $account_info);
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

    public function get_app_exchange(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('telco', 'ip', 'language', 'user_agent', 'platform');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('PaymentModel');
                $result = $this->CI->PaymentModel->get_app_exchange();
                $this->_response = new MEAPI_Response_APIResponse($request, 'REQUEST_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function ntp(MEAPI_RequestInterface $request) {
        $this->_response = new MEAPI_Response_APIResponse($request, 'NTP', array('timestamps' => time()));
    }

    public function verify_sandbox(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request) == TRUE) {
            $params = $request->input_request();
            $needle = array('mobo_id', 'mobo_service_id', 'service_id', 'type', 'money', 'info');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                $this->_response = $this->CI->MDeposit->verify_sandbox($request);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function receive_banking_ipn(MEAPI_RequestInterface $request) {
        $params = $request->input_request();
        $this->CI->load->MEAPI_Library('BankGateway/Bank_mecorp', 'bank_gateway');
        $arrParamIPN = array(
            'par_ID' => $params['par_id'],
            'par_TransID' => $params['par_transid'],
            'par_Amount' => $params['par_amount'],
            'par_SLType' => $params['par_sltype'],
            'par_ResponseCode' => $params['par_responsecode'],
            'par_Data' => $params['par_data'],
        );
        if ($this->CI->bank_gateway->validate_ipn($arrParamIPN, $params['par_signature']) == TRUE) {
            $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
            $tmp = json_decode(base64_decode($params['par_data']), TRUE);
            $banking_info = array(
                'banking_transaction' => $tmp['partner_trans_id'],
                'mobo_id' => $tmp['username'],
                'bank_type' => $tmp['bank_type'],
                'bank_code' => $tmp['bank_code'],
                'info' => json_decode($tmp['data'], TRUE)
            );
            define('SCOPE_ID', $banking_info['info']['service_id']);
            $output = $this->CI->MDeposit->verify_banking($request, $banking_info);
            $arrOutput = $output->getArray();
            $tmp_data = json_decode($tmp['data'], TRUE);
            $callback = $tmp_data['callback'];
            if ($arrOutput['code'] == 110) {
                $data = array(
                    'money' => $arrOutput['data']['money'],
                    'credit' => $arrOutput['data']['credit'],
                    'balance' => $arrOutput['data']['balance'],
                    'message' => $arrOutput['data']['message'],
                    'unit' => $arrOutput['data']['unit'],
                    'language' => $banking_info['info']['language'],
                    'callback' => $callback,
                    'status' => TRUE
                );
                $arrResponse = array(
                    'code' => 0,
                    'msg' => $data['message'],
                );
            } else {
                $data = array(
                    'message' => $arrOutput['data']['message'],
                    'language' => $banking_info['info']['language'],
                    'callback' => $callback
                );
                $arrResponse = array(
                    'code' => 0,
                    'msg' => $data['message'],
                );
            }
        } else {
            $arrResponse = array(
                'code' => 1,
                'msg' => 'Thông tin giao dịch không hợp lệ',
            );
        }
        @header('Content-type: application/json');
        $this->_response = new MEAPI_Response_HTMLResponse($request, json_encode($arrResponse));
    }

    public function getPlayload(){
        $string = file_get_contents('php://input');
        $input = json_decode($string, true);
        if($input == false){
            return array();
        }
        return $input;
    }
    private function listUrlCallBack(){
        return array(
            "http://local.misc.mobo.vn/v1.0/8595/notifycallback"
        );
    }
    public function a8595Notification(MEAPI_RequestInterface $request) {


        //$this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
        try {
            $params = $this->getPlayload();
            //write log
            MEAPI_Log::writeCsv($params, 'a8595Notification');
            if ($params == false) {
                $params = $request->input_request();
                if (!is_array($params["data"]) && is_json($params["data"]) == true) {
                    if (urlencode(urldecode($params["data"])) === $params["data"]) {
                        $params = json_decode(urldecode($params["data"]), true);
                    } else {
                        $params = json_decode($params["data"], true);
                    }
                } elseif (is_array($params["data"])) {
                    $params = $params["data"];
                }
            }

            if (!isset($params["method"]) && strtolower($params["method"]) != 'scratchcard') {
                header("HTTP/1.0 201 Method invalid.");
                die;
            }
            if (!isset($params["transaction"]) && empty($params["transaction"]) === true) {
                header("HTTP/1.0 201 Data invalid.");
                die;
            }

            if (!isset($params["orderinfo"]) && empty($params["orderinfo"]) === true) {
                header("HTTP/1.0 201 Data invalid.");
                die;
            }


            $this->CI->load->MEAPI_Model('DepositModel');

            $notifyData = $params;
            $deposit_transaction = $notifyData["transaction"];
            //get query tracstionid
            $logRequest = $this->CI->DepositModel->check_card_deposit_transaction($deposit_transaction);

            if ($logRequest == false) {
                echo '1';
                header("HTTP/1.0 201 Transaction not exists.");
                die;
            }
            if ($logRequest["status"] == 1) {
                echo '2';
                header("HTTP/1.0 200 Transaction complete before.");
                die;
            }
            if($logRequest["status_code"] == 1 && $logRequest["status"] != 1){
                echo '3';
                header("HTTP/1.0 200 Transaction executing.");
                die;
            }
            //var_dump($logRequest);die;

            $params['status_code'] = 2;

            if ($notifyData["status"] == 1) {
                //update status = 1
                $params_update = array(
                    "status_code" => $notifyData["status"],
                    "money" => $notifyData["money"],
                    "response_time" => date("Y-m-d H:i:s", time()),
                    "notify_url" => $this->getUrl()
                );
                $this->CI->DepositModel->update_card_8595($deposit_transaction,$params_update );

                $amount = $notifyData["money"];
                #prepare request mpay to mopay

                $payParams = array(
                    "access_token"=>$logRequest['access_token'],
                    "order_id" => $deposit_transaction,
                    "transaction_id" => $deposit_transaction,
                    "channel" => $logRequest["channel"],
                    "platform" => $logRequest["platform"],
                    "user_agent" => $logRequest["user_agent"],
                    "ip" => $logRequest["ip_user"],
                    "money" => $amount,
                    "amount" => $amount,
                    "lang" => $logRequest["language"],
                    "telco" => $logRequest["telco"],
                    "mobo_id"=>$logRequest['mobo_id'],
                    "device_id" => $logRequest["device_id"],
                    "info" => $logRequest["game_info"],
                    "direct" => 1
                );
                if (empty($payParams["channel"]))
                    $payParams["channel"] = "1|me|ref|1.0";

                $queryAppId = $logRequest["service_id"];
                if ($queryAppId == 106) {
                    $queryAppId = "mgh";
                }

                $this->CI->load->MEAPI_Library('Mopay/MDeposit', 'MDeposit');
                $contentData = $this->CI->MDeposit->verify_8595_callback($request, $payParams);


                if ($contentData["code"] == 110) {
                    $params['status_code'] = 1;
                    $params = array_merge($params,$contentData['data']);
                }
            } else {
                $this->CI->DepositModel->update_card_8595(
                    $deposit_transaction, array(
                    "status_code" => $notifyData["status"],
                    "status" => 2,
                    "money" => 0,
                    "amount"=> 0,
                    "response_time" => date("Y-m-d H:i:s", time()),
                    "notify_url" => $this->getUrl() . "/?" . http_build_query($params)
                ));

            }
            //callback push client
            $getCallBack = $this->listUrlCallBack();

            if($getCallBack){
                $this->CI->load->MEAPI_library('curl');
                foreach ($getCallBack as $k=>$v){
                    $this->CI->curl->post($v,$params);
                }
            }
            echo '3';die;
            header("HTTP/1.0 200 Commit success.");
            die;
        } catch (Exception $ex) {
            header("HTTP/1.0 500 ");
            die;
        }
    }
    public function getUrl() {
        return 'https://'
            . $_SERVER['HTTP_HOST'] . $this->getPath()
            . ((count($this->getQueryParams()) > 0) ? ('/?'
                . http_build_query($this->getQueryParams())) : '/');
    }
    public function getPath() {
        if (isset($_SERVER["REDIRECT_URL"]) && !empty($_SERVER["REDIRECT_URL"]))
            return $_SERVER["REDIRECT_URL"];
        elseif (isset($_SERVER["PATH_INFO"]) && !empty($_SERVER["PATH_INFO"]))
            return $_SERVER["PATH_INFO"];
        elseif (isset($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"]))
            return $_SERVER["REQUEST_URI"];
        elseif (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"]))
            return $_SERVER["HTTP_REFERER"];
        return '';
    }
    public function getQueryParams() {

        $querys = null;
        if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
            $querys = $_SERVER["QUERY_STRING"];
        } elseif (isset($_SERVER["REDIRECT_QUERY_STRING"]) && !empty($_SERVER["REDIRECT_QUERY_STRING"])) {
            $querys = $_SERVER["REDIRECT_QUERY_STRING"];
        } else {
            $_SERVER["REQUEST_URI"];
        }
        $params = array();
        if ($querys == true && empty($querys) == false) {
            parse_str($querys, $parts);
            if (isset($parts['query'])) {
                $retained_params = explode('&', $parts['query']);
                $params = array_merge($params, $retained_params);
            } else {
                $params = $parts;
            }
        }
        return $params;
    }
}
