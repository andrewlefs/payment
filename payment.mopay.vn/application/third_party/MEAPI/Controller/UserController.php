<?php

class MEAPI_Controller_UserController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_UserInterface {

    public function authorize(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('phone', 'password', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->MEAPI_Library('MoboUser');
                $this->CI->load->helper('phone_helper');
                $this->CI->load->MEAPI_Helper('transaction');

                $params['phone'] = format_phone($params['phone']);
                $params['password'] = md5($params['password']);
                if (is_numeric($params['phone']) === FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'PHONE_INVALID');
                    return;
                }

                $this->CI->load->library('cache');
                $cache_system = $this->CI->cache->load('memcache', 'system_info');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                $cache_info = $cache_system->get('AUTHORIZE_FAIL_' . $params['phone'] . $params['device_id']);
                $count = empty($cache_info) == TRUE ? 1 : $cache_info;

                if ($count > 5) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'LIMIT_AUTHORIZE_REACHED');
                    return;
                }

                $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                $account_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($params['phone']));

                if (empty($account_info) == FALSE) {
                    $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                    $cache_user->append_key($key_mobo_phone, $group_mobo);
                }

                if ($account_info['password'] == $params['password']) {
                    if (SERVICE_STATE == MOBO_SERVICE) {
                        $mobo_service = $this->CI->MoboUser->init_mobo_service($account_info['mobo_id'], $params['device_id'], $params['channel'], $account_info['fullname']);
                        if (empty($mobo_service['mobo_service_id']) === TRUE) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                            return;
                        }
                        if ($mobo_service['is_first'] === TRUE) {
                            $params['is_first'] = TRUE;
                        }
                    }

                    $data_insert = $this->CI->MoboUser->make_access_token($account_info['mobo_id'], $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                    $access_token = $this->CI->UserModel->register_access_token($data_insert);

                    if (empty($access_token) == FALSE) {
                        $this->CI->UserModel->update_last_login_time($mobo_service['service_id'], $mobo_service['mobo_service_id']);
                        $response = array(
                            'mobo_id' => strval($account_info['mobo_id']),
                            'access_token' => $data_insert['access_token'],
                            'fullname' => $account_info['fullname'],
                        );

                        $this->CI->load->MEAPI_Library('InsightAPI');
                        $params['mobo_id'] = $account_info['mobo_id'];
                        $params['mobo_service_id'] = $mobo_service['mobo_service_id'];
                        $this->CI->InsightAPI->login($params);

                        $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_SUCCESS', $response);
                        return;
                    }
                }
                $count = $count + 1;
                $cache_system->save('AUTHORIZE_FAIL_' . $params['phone'] . $params['device_id'], $count, 60 * 5);
                $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                return;
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function authorize_service(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('token_login', 'mobo_id', 'mobo_service_id', 'access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->MEAPI_Library('MoboUser');
                $this->CI->load->MEAPI_Helper('transaction');
                $this->CI->load->library('Crypt');

                $params['token_login'] = base64_decode($params['token_login']);
                $private_token_key = MEAPI_Config_Token::getTokenKey();
                $data_decrypted = $this->CI->crypt->Decrypt($params['token_login'], $private_token_key);

                $data = json_decode($data_decrypted, TRUE);

                $access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
                if (empty($access_token) == TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                    return;
                } else {
                    if ($access_token['mobo_service_id'] == $params['mobo_service_id']) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_SAME_ACCOUNT');
                        return;
                    }
                }

                $account_info = $this->CI->UserModel->get_user_by_mobo_id($data['mobo_id']);
                if (empty($account_info) == FALSE) {
                    if (SERVICE_STATE == MOBO_SERVICE) {
                        $mobo_service = $this->CI->MoboUser->init_mobo_service($account_info['mobo_id'], $params['device_id'], $params['channel'], $account_info['fullname']);
                        if (empty($mobo_service['mobo_service_id']) === TRUE) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                            return;
                        }
                    }

                    $data_insert = $this->CI->MoboUser->make_access_token($account_info['mobo_id'], $params['mobo_service_id'], $params, EXPIRES_1MONTH);
                    $access_token = $this->CI->UserModel->register_access_token($data_insert);

                    if (empty($access_token) == FALSE) {
                        $response = array(
                            'mobo_id' => strval($account_info['mobo_id']),
                            'access_token' => $data_insert['access_token'],
                            'fullname' => $account_info['fullname'],
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_SUCCESS', $response);
                        return;
                    }
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                return;
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function authorize_device(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->MEAPI_Helper('transaction');
                $this->CI->load->MEAPI_Library('MoboUser');
                $this->CI->load->helper('phone_helper');

                if ($params['device_id'] == '0000-0000-0000-000') {
                    $params['device_id'] = $params['device_id'] . md5(time());
                }

                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                $access_token = $this->CI->UserModel->get_access_token_by_device($params['device_id']);
                if (empty($access_token) == FALSE) {
                    foreach ($access_token as $key => $value) {
                        $flag_error = FALSE;
                        //$account_info = $this->CI->UserModel->get_user_by_mobo_id($value['mobo_id']);

                        $key_mobo_id = 'MOBO_ID_' . $value['mobo_id'];
                        $account_info = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_user_by_mobo_id', array($value['mobo_id']));

                        if (empty($account_info) == FALSE) {
                            $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                            $cache_user->append_key($key_mobo_id, $group_mobo);
                        }

                        if (SERVICE_STATE == MOBO_SERVICE) {
                            $mobo_service = $this->CI->MoboUser->init_mobo_service($account_info['mobo_id'], $params['device_id'], $params['channel'], $account_info['fullname']);
                            if (empty($mobo_service['mobo_service_id']) === TRUE) {
                                $flag_error = TRUE;
                            }
                        }
                        if ($flag_error === FALSE) {
                            $data_insert = $this->CI->MoboUser->make_access_token($account_info['mobo_id'], $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                            $result = $this->CI->UserModel->register_access_token($data_insert);
                            if (empty($result) == FALSE) {
                                $phone = empty($account_info['phone']) == FALSE ? $account_info['phone'] : $account_info['temporary'];
                                $is_active = empty($account_info['phone']) == FALSE ? TRUE : FALSE;

                                if (empty($phone) == FALSE) {
                                    $response[] = array(
                                        'mobo_id' => strval($value['mobo_id']),
                                        'access_token' => $data_insert['access_token'],
                                        'fullname' => $account_info['fullname'],
                                        'phone' => show_phone($phone),
                                        'is_active' => $is_active,
                                    );
                                }
                            }
                        }
                    }

                    $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_SUCCESS', $response);
                    return;
                }

                $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FAIL');
                return;
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function authorize_facebook(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('fb_access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('FacebookAPI');
                $this->CI->load->MEAPI_Library('MoboUser');
                $data = $this->CI->FacebookAPI->verify_access_token($params['fb_access_token']);
                if (empty($data['error']) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'FB_ACCESS_TOKEN_INVALID', $data['error']);
                } else {
                    /* if ($data['verified'] == FALSE) {
                      $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_NOT_ACTIVED');
                      return;
                      } */
                    $this->CI->load->MEAPI_Model('UserModel');
                    //$facebook_info = $this->CI->UserModel->get_account_by_fb_id($data['id']);

                    $this->CI->load->library('cache');
                    $cache_user = $this->CI->cache->load('memcache', 'user_info');

                    $key_mobo_fb = 'MOBO_FB_' . $data['id'];
                    $facebook_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($data['id'], $data['token_for_business']));

                    if (empty($facebook_info) == TRUE) {
                        $facebook_id = $this->CI->FacebookAPI->get_original_facebook_id($data['id']);

                        $key_mobo_fb = 'MOBO_FB_' . $facebook_id;
                        $facebook_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($facebook_id));

                        if (empty($facebook_info) == TRUE) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FACEBOOK_FAIL');
                            return;
                        }
                    }

                    if (empty($facebook_info['facebook_token']) == TRUE) {
                        $data_update['facebook_token'] = $data['token_for_business'];
                        $this->CI->UserModel->update_facebook_token($data_update, $facebook_info['facebook_id']);
                    }

                    $group_mobo = 'MOBO_' . $facebook_info['mobo_id'];
                    $cache_user->append_key($key_mobo_fb, $group_mobo);

                    //$account_info = $this->CI->UserModel->get_user_by_mobo_id($facebook_info['mobo_id']);
                    $key_mobo_id = 'MOBO_ID_' . $facebook_info['mobo_id'];
                    $account_info = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_user_by_mobo_id', array($facebook_info['mobo_id']));

                    if (empty($account_info) == TRUE) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FACEBOOK_FAIL');
                    } else {
                        $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                        $cache_user->append_key($key_mobo_id, $group_mobo);

                        if (SERVICE_STATE == MOBO_SERVICE) {
                            $mobo_service = $this->CI->MoboUser->init_mobo_service($account_info['mobo_id'], $params['device_id'], $params['channel'], $account_info['fullname']);
                            if (empty($mobo_service['mobo_service_id']) === TRUE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'SYSTEM_ERROR');
                                return;
                            }
                        }
                        $arrAccessToken = $this->CI->MoboUser->make_access_token($account_info['mobo_id'], $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                        $access_token = $this->CI->UserModel->register_access_token($arrAccessToken);

                        if (empty($access_token) === FALSE) {
                            $phone = empty($account_info['phone']) == FALSE ? $account_info['phone'] : $account_info['temporary'];
                            $is_active = empty($account_info['phone']) == FALSE ? TRUE : FALSE;

                            $result = array(
                                'mobo_id' => strval($arrAccessToken['mobo_id']),
                                'phone' => $phone,
                                'is_active' => $is_active,
                                'fullname' => $account_info['fullname'],
                                'access_token' => $arrAccessToken['access_token']
                            );

                            $this->CI->load->MEAPI_Library('InsightAPI');
                            $params['mobo_id'] = $arrAccessToken['mobo_id'];
                            $params['mobo_service_id'] = $mobo_service['mobo_service_id'];
                            $this->CI->InsightAPI->login($params);

                            $this->_response = new MEAPI_Response_APIResponse($request, 'AUTHORIZE_FACEBOOK_SUCCESS', $result);
                            return;
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'SYSTEM_ERROR');
                            return;
                        }
                    }
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS', array_diff($needle, array_keys($params)));
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function register_facebook(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('fb_access_token', 'phone', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Library('FacebookAPI');
                $this->CI->load->MEAPI_Library('MoboUser');
                $data = $this->CI->FacebookAPI->verify_access_token($params['fb_access_token']);
                if (empty($data['error']) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID', $data['error']);
                } else {
                    /* if ($data['verified'] == FALSE) {
                      $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_NOT_ACTIVED');
                      return;
                      } */

                    $this->CI->load->MEAPI_Model('UserModel');
                    $this->CI->load->helper('phone_helper');
                    $params['phone'] = format_phone($params['phone']);
                    $this->CI->load->library('cache');
                    $cache_user = $this->CI->cache->load('memcache', 'user_info');
                    //$account_info = $this->CI->UserModel->get_user_by_phone($params['phone']);

                    $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                    $account_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($params['phone']));

                    if (empty($account_info) == TRUE) {
                        $total_account = $this->CI->UserModel->get_account_by_device_id($params['device_id']);
                        if (count($total_account) > 100) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'LIMIT_ACCOUNT_REACHED');
                            return;
                        }

                        $key_mobo_fb = 'MOBO_FB_' . $data['id'];
                        $facebook_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($data['id'], $data['token_for_business']));

                        if (empty($facebook_info) == FALSE) {
                            $response = array(
                                'mobo_id' => strval($facebook_info['mobo_id']),
                                'fullname' => strval($facebook_info['fullname']),
                                'facebook_id' => $facebook_info['facebook_id'],
                            );
                            $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED', $response);
                            return;
                        } else {
                            $facebook_id = $this->CI->FacebookAPI->get_original_facebook_id($data['id']);
                            $key_mobo_fb = 'MOBO_FB_' . $facebook_id;
                            $facebook_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($facebook_id));

                            if (empty($facebook_info) == FALSE) {
                                $response = array(
                                    'mobo_id' => strval($facebook_info['mobo_id']),
                                    'fullname' => strval($facebook_info['fullname']),
                                    'facebook_id' => $facebook_info['facebook_id'],
                                );
                                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED', $response);
                                return;
                            }
                        }

                        $arrInsert = array(
                            'temporary' => $params['phone'],
                            'device_id' => $params['device_id']
                        );
                        $mobo_id = $this->CI->UserModel->register_quickly($arrInsert);
                        if (is_numeric($mobo_id)) {
                            $this->CI->load->MEAPI_Helper('transaction');
                            $arrInsert = array(
                                'mobo_id' => $mobo_id,
                                'facebook_id' => $data['id'],
                                'facebook_token' => $data['token_for_business']
                            );
                            $update = $this->CI->UserModel->insert_facebook($arrInsert);
                            if (empty($update) === FALSE) {
                                if (SERVICE_STATE == MOBO_SERVICE) {
                                    $mobo_service = $this->CI->MoboUser->init_mobo_service($arrInsert['mobo_id'], $params['device_id'], $params['channel'], NULL);
                                    if (empty($mobo_service['mobo_service_id']) === TRUE) {
                                        $this->_response = new MEAPI_Response_APIResponse($request, 'SYSTEM_ERROR');
                                        return;
                                    }
                                }
                                $arrAccessToken = $this->CI->MoboUser->make_access_token($arrInsert['mobo_id'], $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                                $access_token = $this->CI->UserModel->register_access_token($arrAccessToken);

                                if (empty($access_token) === FALSE) {

                                    $valid_phone = get_telco_by_phone($params['phone']);
                                    if ($valid_phone == TRUE) {
                                        $this->sendActiveCode($arrAccessToken['mobo_id'], $params['phone']);
                                    }

                                    $result = array(
                                        'mobo_id' => strval($arrAccessToken['mobo_id']),
                                        'access_token' => $arrAccessToken['access_token']
                                    );
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FACEBOOK_SUCCESS', $result);
                                    return;
                                } else {
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'SYSTEM_ERROR');
                                    return;
                                }
                            } else {
                                // TODO: LOG ? Remove MoboID
                            }
                        }
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FACEBOOK_FAIL');
                    } else {

                        $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                        $cache_user->append_key($key_mobo_phone, $group_mobo);

                        if (empty($account_info['facebook_id']) == TRUE) {
                            $response = array(
                                'mobo_id' => strval($account_info['mobo_id']),
                                'fullname' => strval($account_info['fullname']),
                            );
                            $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_CONNECTED_REQUIRED', $response);
                        } else {
                            $response = array(
                                'mobo_id' => strval($account_info['mobo_id']),
                                'fullname' => strval($account_info['fullname']),
                                'facebook_id' => $account_info['facebook_id'],
                            );
                            $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED', $response);
                        }
                    }
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function map_account_facebook(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('fb_access_token', 'phone', 'password', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->helper('phone_helper');
                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                $params['phone'] = format_phone($params['phone']);
                $params['password'] = md5($params['password']);

                //$account_info = $this->CI->UserModel->get_user_by_phone($params['phone']);
                $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                $account_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($params['phone']));

                if (empty($account_info) == FALSE) {
                    $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                    $cache_user->append_key($key_mobo_phone, $group_mobo);
                }

                if (empty($account_info['facebook_id']) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED');
                    return;
                }

                if ($account_info['password'] == $params['password']) {
                    $this->CI->load->MEAPI_Library('FacebookAPI');
                    $data_fb = $this->CI->FacebookAPI->verify_access_token($params['fb_access_token']);

                    if (empty($data_fb['error']) == FALSE) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'FB_ACCESS_TOKEN_INVALID', $data_fb['error']);
                    } else {
                        /* if ($data_fb['verified'] == FALSE) {
                          $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_NOT_ACTIVED');
                          return;
                          } */

                        //$data_info = $this->CI->UserModel->get_account_by_fb_id($data_fb['id']);

                        $key_mobo_fb = 'MOBO_FB_' . $data_fb['id'];
                        $data_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($data_fb['id'], $data_fb['token_for_business']));

                        if (empty($data_info) == TRUE) {

                            $facebook_id = $this->CI->FacebookAPI->get_original_facebook_id($data_fb['id']);
                            $key_mobo_fb = 'MOBO_FB_' . $facebook_id;
                            $facebook_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($facebook_id));

                            if (empty($facebook_info) == FALSE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED');
                                return;
                            }

                            $arrInsert = array(
                                'mobo_id' => $account_info['mobo_id'],
                                'facebook_id' => $data_fb['id']
                            );
                            $result = $this->CI->UserModel->insert_facebook($arrInsert);
                            if (empty($result) == TRUE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'MAP_ACCOUNT_FAIL');
                            } else {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'MAP_ACCOUNT_SUCCESS');
                                $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                                $cache_user->delete_group($group_mobo);

                                $group_mobo = 'MOBO_GROUP_AC' . $account_info['mobo_id'];
                                $cache_user->delete_group($group_mobo);
                            }
                        } else {
                            $group_mobo = 'MOBO_' . $data_info['mobo_id'];
                            $cache_user->append_key($key_mobo_fb, $group_mobo);

                            $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED');
                        }
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

    public function link_facebook(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('fb_access_token', 'access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->helper('phone_helper');
                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                //$access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
                $key_mobo_ac = 'MOBO_AC_' . md5($params['access_token']);
                $access_token = $cache_user->store($key_mobo_ac, $this->CI->UserModel, 'get_user_by_access_token', array($params['access_token']));

                if (empty($access_token) == FALSE) {

                    $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                    $cache_user->append_key($key_mobo_ac, $group_mobo);

                    $this->CI->load->MEAPI_Library('FacebookAPI');
                    $data_fb = $this->CI->FacebookAPI->verify_access_token($params['fb_access_token']);
                    if (empty($data_fb['error']) == FALSE) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID', $data_fb['error']);
                    } else {
                        //$data_info = $this->CI->UserModel->get_account_by_fb_id($data_fb['id']);
                        $key_mobo_fb = 'MOBO_FB_' . $data_fb['id'];
                        $data_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($data_fb['id'], $data_fb['token_for_business']));

                        if (empty($data_info) == TRUE) {

                            $facebook_id = $this->CI->FacebookAPI->get_original_facebook_id($data_fb['id']);
                            $key_mobo_fb = 'MOBO_FB_' . $facebook_id;
                            $facebook_info = $cache_user->store($key_mobo_fb, $this->CI->UserModel, 'get_account_by_fb_id', array($facebook_id));

                            if (empty($facebook_info) == FALSE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED');
                                return;
                            }

                            //$account_info = $this->CI->UserModel->get_user_by_mobo_id($access_token['mobo_id']);
                            $key_mobo_id = 'MOBO_ID_' . $access_token['mobo_id'];
                            $account_info = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_user_by_mobo_id', array($access_token['mobo_id']));

                            $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                            $cache_user->append_key($key_mobo_id, $group_mobo);

                            $dataInsert = array(
                                'mobo_id' => $account_info['mobo_id'],
                                'facebook_id' => $data_fb['id']
                            );
                            $result = $this->CI->UserModel->insert_facebook($dataInsert);
                            if (empty($result) == TRUE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'MAP_ACCOUNT_FAIL');
                            } else {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'MAP_ACCOUNT_SUCCESS');
                            }
                        } else {
                            $group_mobo = 'MOBO_' . $data_info['mobo_id'];
                            $cache_user->append_key($key_mobo_fb, $group_mobo);

                            $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_FACEBOOK_ALREADY_USED');
                        }
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'MAP_ACCOUNT_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function register_quickly(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            if (is_required($params, array('lang', 'user_agent', 'channel', 'platform', 'version', 'device_id', 'phone')) == TRUE) {
                $this->CI->load->helper('phone_helper');
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->MEAPI_Library('MoboUser');
                $params['phone'] = format_phone($params['phone']);

                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                if (is_phone_register($params['phone']) == FALSE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'PHONE_INVALID');
                    return;
                }

                //$user_info = $this->CI->UserModel->get_user_by_phone($params['phone']);
                $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                $user_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($params['phone']));

                if (empty($user_info) === FALSE) {
                    $group_mobo = 'MOBO_' . $user_info['mobo_id'];
                    $cache_user->append_key($key_mobo_phone, $group_mobo);

                    $arrResponse = array(
                        'mobo_id' => (string)$user_info['mobo_id'],
                        'fullname' => (string)$user_info['fullname'],
                        'birthday' => (string)$user_info['birthday']
                    );
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_EXIST', $arrResponse);
                    return;
                }

                $account_info = $this->CI->UserModel->get_account_by_device_id($params['device_id']);

                $is_temp_account = FALSE;
                if (empty($account_info) == FALSE) {
                    foreach ($account_info as $value) {
                        if (empty($value['phone']) == TRUE AND $value['temporary'] == $params['phone']) {
                            $mobo_id = strval($value['mobo_id']);
                            $fullname = $value['fullname'];
                            $is_temp_account = TRUE;
                            break;
                        }
                    }
                }

                if ($is_temp_account == TRUE) {
                    $this->CI->load->MEAPI_Helper('transaction');

                    if (SERVICE_STATE == MOBO_SERVICE) {
                        $mobo_service = $this->CI->MoboUser->init_mobo_service($mobo_id, $params['device_id'], $params['channel'], $fullname);
                        if (empty($mobo_service['mobo_service_id']) === TRUE) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FAIL');
                            return;
                        }
                    }

                    $arrAccessToken = $this->CI->MoboUser->make_access_token($mobo_id, $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                    $access_token = $this->CI->UserModel->register_access_token($arrAccessToken);
                    if (empty($access_token) === FALSE) {
                        $result = array(
                            'mobo_id' => strval($mobo_id),
                            'access_token' => $arrAccessToken['access_token'],
                            'fullname' => $fullname,
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_SUCCESS', $result);
                        return;
                    }
                } else {
                    if (count($account_info) > 100) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'LIMIT_ACCOUNT_REACHED');
                        return;
                    } else {
                        $arrInsert = array(
                            'temporary' => $params['phone'],
                            'device_id' => $params['device_id']
                        );
                        $mobo_id = $this->CI->UserModel->register_quickly($arrInsert);
                        if (is_numeric($mobo_id)) {
                            $this->CI->load->MEAPI_Helper('transaction');
                            if (SERVICE_STATE == MOBO_SERVICE) {
                                $mobo_service = $this->CI->MoboUser->init_mobo_service($mobo_id, $params['device_id'], $params['channel'], NULL);
                                if (empty($mobo_service['mobo_service_id']) === TRUE) {
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FAIL');
                                    return;
                                }
                            }
                            $arrAccessToken = $this->CI->MoboUser->make_access_token($mobo_id, $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                            $access_token = $this->CI->UserModel->register_access_token($arrAccessToken);
                            if (empty($access_token) === FALSE) {
                                $valid_phone = get_telco_by_phone($params['phone']);
                                if ($valid_phone == TRUE) {
                                    $this->sendActiveCode($mobo_id, $params['phone']);
                                }

                                $result = array(
                                    'mobo_id' => strval($arrAccessToken['mobo_id']),
                                    'access_token' => $arrAccessToken['access_token']
                                );

                                $this->CI->load->MEAPI_Library('InsightAPI');
                                $params['mobo_id'] = $arrAccessToken['mobo_id'];
                                $params['mobo_service_id'] = $mobo_service['mobo_service_id'];
                                $this->CI->InsightAPI->login($params);

                                $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_SUCCESS', $result);
                                return;
                            }
                        }
                    }
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FAIL');
                return;
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function register_guest(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            if (is_required($params, array('lang', 'user_agent', 'channel', 'platform', 'version', 'device_id')) == TRUE) {
                $this->CI->load->helper('phone_helper');
                $this->CI->load->MEAPI_Library('MoboUser');
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->MEAPI_Library('MoboUser');

                $account_info = $this->CI->UserModel->get_account_guest_by_device_id($params['device_id']);
                if (empty($account_info) == FALSE) {
                    $mobo_id = $account_info['mobo_id'];
                    if (empty($account_info['phone']) == FALSE) {
                        $response = array(
                            'mobo_id' => strval($mobo_id),
                            'fullname' => $account_info['fullname'],
                            'phone' => show_phone($account_info['phone']),
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_ALREADY_ACTIVE', $response);
                        return;
                    }
                } else {
                    $arrInsert = array(
                        'temporary' => '',
                        'device_id' => $params['device_id'],
                        'state' => STATE_GUEST,
                    );
                    $mobo_id = $this->CI->UserModel->register_guest($arrInsert);
                    if (is_numeric($mobo_id) == FALSE) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FAIL');
                        return;
                    }
                }

                $this->CI->load->MEAPI_Helper('transaction');
                if (SERVICE_STATE == MOBO_SERVICE) {
                    $mobo_service = $this->CI->MoboUser->init_mobo_service($mobo_id, $params['device_id'], $params['channel'], NULL);
                    if (empty($mobo_service['mobo_service_id']) === TRUE) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_FAIL');
                        return;
                    }
                }
                $arrAccessToken = $this->CI->MoboUser->make_access_token($mobo_id, $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                $access_token = $this->CI->UserModel->register_access_token($arrAccessToken);
                if (empty($access_token) === FALSE) {
                    $response = array(
                        'mobo_id' => (string)$arrAccessToken['mobo_id'],
                        'access_token' => $arrAccessToken['access_token']
                    );

                    $this->CI->load->MEAPI_Library('InsightAPI');
                    $params['mobo_id'] = $arrAccessToken['mobo_id'];
                    $params['mobo_service_id'] = $mobo_service['mobo_service_id'];
                    $this->CI->InsightAPI->login($params);

                    $this->_response = new MEAPI_Response_APIResponse($request, 'REGISTER_SUCCESS', $response);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'SYSTEM_ERROR');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function verify_access_token(MEAPI_RequestInterface $request) {
        $params = $request->input_request();
        if (is_required($params, array('access_token')) == TRUE) {
            $this->CI->load->MEAPI_Model('UserModel');
            $this->CI->load->library('cache');
            $cache_user = $this->CI->cache->load('memcache', 'user_info');

            //$access_token = $this->CI->UserModel->verify_access_token($params['access_token']);
            $key_mobo_ac = 'MOBO_AC_' . md5($params['access_token']);
            $access_token = $cache_user->store($key_mobo_ac, $this->CI->UserModel, 'verify_access_token', array($params['access_token']));

            if (empty($access_token) === FALSE) {
                $group_mobo = 'MOBO_GROUP_AC_' . $access_token['mobo_id'];
                $cache_user->append_key($key_mobo_ac, $group_mobo);

                //$account_info = $this->CI->UserModel->get_info($access_token['mobo_id']);
                $key_mobo_id = 'MOBO_ID_' . $access_token['mobo_id'];
                $account_info = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_info', array($access_token['mobo_id']));

                $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                $cache_user->append_key($key_mobo_id, $group_mobo);

                $active = empty($account_info['phone']) == TRUE ? FALSE : TRUE;
                $linked['facebook'] = empty($account_info['facebook_id']) == TRUE ? '' : $account_info['facebook_id'];
                $result = array(
                    'mobo_id' => strval($access_token['mobo_id']),
                    'mobo_service_id' => $access_token['mobo_service_id'] ? strval($access_token['mobo_service_id']) : NULL,
                    'data' => $access_token['data'],
                    'active' => $active,
                    'linked' => $linked,
                    'service_id' => $account_info['service_id']
                );
                if ($result['mobo_service_id'] === NULL)
                    unset($result['mobo_service_id']);


                if ($access_token['service_id'] == 100) {
                    $account_active = $this->CI->UserModel->get_account_active($access_token['mobo_id']);
                    if (empty($account_active) == TRUE) {
                        $result['trial'] = array(
                            'is_active' => FALSE,
                            'link' => 'http://id.mobo.dev/giftcode/'
                        );
                    } else {
                        $result['trial'] = array(
                            'is_active' => TRUE,
                            'link' => 'http://id.mobo.dev/giftcode/'
                        );
                    }
                }

                $this->_response = new MEAPI_Response_APIResponse($request, 'VERIFY_ACCESS_TOKEN_SUCCESS', $result);
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'VERIFY_ACCESS_TOKEN_FAIL');
            }
        } else {
            $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
        }
    }

    public function check_mobo_id(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('mobo_id', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->helper('phone_helper');

                if (is_numeric($params['mobo_id'])) {
                    $this->CI->load->library('cache');
                    $cache_user = $this->CI->cache->load('memcache', 'user_info');

                    //$account_info = $this->CI->UserModel->get_user_by_mobo_id($params['mobo_id']);
                    $key_mobo_id = 'MOBO_ID_' . $params['mobo_id'];
                    $account_info = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_user_by_mobo_id', array($params['mobo_id']));

                    if (empty($account_info) == FALSE) {
                        $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                        $cache_user->append_key($key_mobo_id, $group_mobo);

                        $is_active = empty($account_info['phone']) == FALSE ? TRUE : FALSE;
                        $response = array(
                            'mobo_id' => strval($params['mobo_id']),
                            'fullname' => $account_info['fullname'],
                            'is_active' => $is_active,
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_MOBO_SUCCESS', $response);
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_MOBO_FAIL');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_MOBO_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function check_phone(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('phone', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->helper('phone_helper');
                $params['phone'] = format_phone($params['phone']);
                $valid_phone = get_telco_by_phone($params['phone']);

                if (is_phone($params['phone']) == TRUE AND $valid_phone == TRUE) {
                    //$account_info = $this->CI->UserModel->get_user_by_phone($params['phone']);

                    $this->CI->load->library('cache');
                    $cache_user = $this->CI->cache->load('memcache', 'user_info');
                    $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                    $account_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($params['phone']));

                    if (empty($account_info) == FALSE) {
                        $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                        $cache_user->append_key($key_mobo_phone, $group_mobo);

                        $response = array(
                            'mobo_id' => strval($account_info['mobo_id']),
                            'fullname' => $account_info['fullname'],
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_PHONE_SUCCESS', $response);
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'CHECK_PHONE_FAIL');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'PHONE_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function send_code_forget(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('phone', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->helper('phone_helper');
                $params['phone'] = format_phone($params['phone']);
                $valid_phone = get_telco_by_phone($params['phone']);

                if (is_phone($params['phone']) == TRUE AND $valid_phone == TRUE) {
                    $this->CI->load->MEAPI_Model('UserModel');
                    $this->CI->load->MEAPI_Library('MoboUser');
                    $this->CI->load->MEAPI_Library('GatewayAPI');

                    $result = $this->CI->UserModel->get_user_by_phone($params['phone']);
                    if (empty($result) == TRUE) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST');
                    } else {

                        $this->CI->load->library('cache');
                        $cache_system = $this->CI->cache->load('memcache', 'system_info');

                        $cache_info['phone'] = $cache_system->get('SEND_CODE_FORGET_' . $params['phone']);
                        $cache_info['device_id'] = $cache_system->get('SEND_CODE_FORGET_' . $params['device_id']);

                        $count['phone'] = empty($cache_info['phone']) == TRUE ? 1 : $cache_info['phone'];
                        $count['device_id'] = empty($cache_info['device_id']) == TRUE ? 1 : $cache_info['device_id'];

                        if ($count['phone'] > 3 OR $count['device_id'] > 3) {
                            $params['phone'] = show_phone($params['phone']);
                            $response = array(
                                'message' => MEAPI_Config_Message::getMessageSendCodeManual($params['phone']),
                                'phone' => MEAPI_Config_Message::getPortSendCodeManual(),
                                'content' => MEAPI_Config_Message::getContentSendCodeManual(),
                            );
                            $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_MANUAL', $response);
                            return;
                        }


                        $active_code = $this->CI->MoboUser->gen_active_code();
                        $data_insert = array(
                            'mobo_id' => $result['mobo_id'],
                            'phone' => $params['phone'],
                            'code' => $active_code,
                        );
                        $result = $this->CI->UserModel->insert_active_code($data_insert);
                        if (empty($result) == FALSE) {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_FORGET_FAIL');
                            $message = MEAPI_Config_Message::getMessageForgetPassword($active_code);
                            $service = '7065';
                            $result = $this->CI->GatewayAPI->send_mt($params['phone'], $service, $message);

                            $count['phone'] = $count['phone'] + 1;
                            $count['device_id'] = $count['device_id'] + 1;
                            $cache_system->save('SEND_CODE_FORGET_' . $params['phone'], $count['phone'], 24 * 60 * 60);
                            $cache_system->save('SEND_CODE_FORGET_' . $params['device_id'], $count['device_id'], 24 * 60 * 60);

                            if ($result == TRUE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_FORGET_SUCCESS');
                                return;
                            }
                        }

                        $params['phone'] = show_phone($params['phone']);
                        $response = array(
                            'message' => MEAPI_Config_Message::getMessageSendCodeManual($params['phone']),
                            'phone' => MEAPI_Config_Message::getPortSendCodeManual(),
                            'content' => MEAPI_Config_Message::getContentSendCodeManual(),
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_MANUAL', $response);
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'PHONE_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function send_code_active(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('phone', 'access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->helper('phone_helper');
                $params['phone'] = format_phone($params['phone']);
                $valid_phone = get_telco_by_phone($params['phone']);

                if (is_phone($params['phone']) == TRUE AND $valid_phone == TRUE) {

                    $this->CI->load->MEAPI_Model('UserModel');
                    $this->CI->load->MEAPI_Library('MoboUser');
                    $this->CI->load->MEAPI_Library('GatewayAPI');

                    $access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
                    if (empty($access_token) == FALSE) {
                        $this->CI->load->library('cache');
                        $cache_system = $this->CI->cache->load('memcache', 'system_info');

                        $cache_info['phone'] = $cache_system->get('SEND_ACTIVE_' . $params['phone']);
                        $cache_info['device_id'] = $cache_system->get('SEND_ACTIVE_' . $params['device_id']);

                        $count['phone'] = empty($cache_info['phone']) == TRUE ? 1 : $cache_info['phone'];
                        $count['device_id'] = empty($cache_info['device_id']) == TRUE ? 1 : $cache_info['device_id'];

                        if ($count['phone'] > 3 OR $count['device_id'] > 3) {
                            $params['phone'] = show_phone($params['phone']);
                            $response = array(
                                'message' => MEAPI_Config_Message::getMessageSendCodeManual($params['phone']),
                                'phone' => MEAPI_Config_Message::getPortSendCodeManual(),
                                'content' => MEAPI_Config_Message::getContentSendCodeManual(),
                            );
                            $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_MANUAL', $response);
                            return;
                        }

                        $active_code = $this->CI->MoboUser->gen_active_code();
                        $data_insert = array(
                            'mobo_id' => $access_token['mobo_id'],
                            'phone' => $params['phone'],
                            'code' => $active_code,
                        );
                        $result = $this->CI->UserModel->insert_active_code($data_insert);

                        $data_update['temporary'] = $params['phone'];
                        $result_update = $this->CI->UserModel->update($access_token['mobo_id'], $data_update);

                        if (empty($result) == FALSE) {
                            $message = MEAPI_Config_Message::getMessageActiveCode($active_code);
                            $service = '7065';
                            $result = $this->CI->GatewayAPI->send_mt($params['phone'], $service, $message);
                            // TODO: Check result
                            $count['phone'] = $count['phone'] + 1;
                            $count['device_id'] = $count['device_id'] + 1;
                            $cache_system->save('SEND_ACTIVE_' . $params['phone'], $count['phone'], 24 * 60 * 60);
                            $cache_system->save('SEND_ACTIVE_' . $params['device_id'], $count['device_id'], 24 * 60 * 60);

                            $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_ACTIVE_SUCCESS');
                            return;
                        }


                        $params['phone'] = show_phone($params['phone']);
                        $response = array(
                            'message' => MEAPI_Config_Message::getMessageSendCodeManual($params['phone']),
                            'phone' => MEAPI_Config_Message::getPortSendCodeManual(),
                            'content' => MEAPI_Config_Message::getContentSendCodeManual(),
                        );
                        $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_MANUAL', $response);
                        return;
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'PHONE_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function forgot_password(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('active_code', 'phone', 'password', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $this->CI->load->helper('phone_helper');

                $params['phone'] = format_phone($params['phone']);

                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                //$user_info = $this->CI->UserModel->get_user_by_phone($params['phone']);
                $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                $user_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($params['phone']));

                if (empty($user_info) == FALSE) {
                    $group_mobo = 'MOBO_' . $user_info['mobo_id'];
                    $cache_user->append_key($key_mobo_phone, $group_mobo);

                    $mobo_id = $user_info['mobo_id'];
                    $data_info = $this->CI->UserModel->get_active_code($params['active_code'], $mobo_id);

                    if (empty($data_info) == FALSE) {
                        $result = $this->CI->UserModel->delete_active_code($mobo_id, $params['active_code']);
                        $params['password'] = md5($params['password']);
                        if ($result == TRUE) {
                            $data_update = array(
                                'password' => $params['password'],
                            );
                            $result = $this->CI->UserModel->update_info($mobo_id, $data_update);
                            $this->CI->load->MEAPI_Helper('transaction');
                            if ($result == TRUE) {
                                $this->CI->load->MEAPI_Library('MoboUser');
                                if (SERVICE_STATE == MOBO_SERVICE) {
                                    $mobo_service = $this->CI->MoboUser->init_mobo_service($user_info['mobo_id'], $params['device_id'], $params['channel'], $user_info['fullname']);
                                    if (empty($mobo_service['mobo_service_id']) === TRUE) {
                                        $this->_response = new MEAPI_Response_APIResponse($request, 'SYSTEM_ERROR');
                                        return;
                                    }
                                }
                                $data_insert = $this->CI->MoboUser->make_access_token($user_info['mobo_id'], $mobo_service['mobo_service_id'], $params, EXPIRES_1MONTH);
                                $access_token = $this->CI->UserModel->register_access_token($data_insert);
                                if (empty($access_token) == FALSE) {
                                    $response = array(
                                        'mobo_id' => strval($user_info['mobo_id']),
                                        'access_token' => $data_insert['access_token'],
                                        'fullname' => $user_info['fullname'],
                                    );
                                }
                                $group_mobo = 'MOBO_' . $user_info['mobo_id'];
                                $cache_user->delete_group($group_mobo);

                                $group_mobo = 'MOBO_GROUP_AC' . $user_info['mobo_id'];
                                $cache_user->delete_group($group_mobo);
                                $this->_response = new MEAPI_Response_APIResponse($request, 'CHANGE_PASSWORD_SUCCESS', $response);
                            } else {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'CHANGE_PASSWORD_FAIL');
                            }
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'CHANGE_PASSWORD_FAIL');
                        }
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_CODE_INVALID');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST');
                }
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function change_password(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'new_password', 'old_password', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');

                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                //$access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
                $key_mobo_ac = 'MOBO_AC_' . md5($params['access_token']);
                $access_token = $cache_user->store($key_mobo_ac, $this->CI->UserModel, 'get_user_by_access_token', array($params['access_token']));

                if (empty($access_token) == FALSE) {
                    $group_mobo = 'MOBO_GROUP_AC_' . $access_token['mobo_id'];
                    $cache_user->append_key($key_mobo_ac, $group_mobo);

                    $mobo_id = $access_token['mobo_id'];

                    //$user_info = $this->CI->UserModel->get_user_by_mobo_id($mobo_id);
                    $key_mobo_id = 'MOBO_ID_' . $access_token['mobo_id'];
                    $user_info = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_user_by_mobo_id', array($access_token['mobo_id']));

                    if (empty($user_info) == FALSE) {
                        $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                        $cache_user->append_key($key_mobo_id, $group_mobo);

                        if ($user_info['password'] == md5($params['old_password'])) {
                            $data_update = array(
                                'password' => md5($params['new_password']),
                            );
                            $result = $this->CI->UserModel->update_info($mobo_id, $data_update);
                            if ($result == TRUE) {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'CHANGE_PASSWORD_SUCCESS');

                                $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                                $cache_user->delete_group($group_mobo);

                                $group_mobo = 'MOBO_GROUP_AC' . $access_token['mobo_id'];
                                $cache_user->delete_group($group_mobo, $key_mobo_ac);
                            } else {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'CHANGE_PASSWORD_FAIL');
                            }
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'PASSWORD_NOT_MATCH');
                        }
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_NOT_EXIST');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function active_account(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('active_code', 'access_token', 'password', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
                if (empty($access_token) == FALSE) {

                    $this->CI->load->library('cache');
                    $cache_system = $this->CI->cache->load('memcache', 'system_info');
                    $cache_user = $this->CI->cache->load('memcache', 'user_info');

                    $cache_info = $cache_system->get('ACTIVE_ACCOUNT_FAIL' . $access_token['mobo_id'] . $params['active_code'] . $params['device_id']);
                    $count = empty($cache_info) == TRUE ? 1 : $cache_info;
                    if ($count > 5) {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'LIMIT_ACTIVE_ACCOUNT_REACHED');
                        return;
                    }

                    $mobo_id = $access_token['mobo_id'];
                    //$account_mobo = $this->CI->UserModel->get_user_by_mobo_id($mobo_id);

                    $key_mobo_id = 'MOBO_ID_' . $mobo_id;
                    $account_mobo = $cache_user->store($key_mobo_id, $this->CI->UserModel, 'get_user_by_mobo_id', array($mobo_id));

                    $group_mobo = 'MOBO_' . $account_mobo['mobo_id'];
                    $cache_user->append_key($key_mobo_id, $group_mobo);

                    if (empty($account_mobo['phone']) == TRUE) {
                        $phone = $account_mobo['temporary'];
                        $data_info = $this->CI->UserModel->get_active_code_by_phone($params['active_code'], $phone);

                        if (empty($data_info) == FALSE) {
                            $now = mktime();
                            $create_time = strtotime($data_info['datecreate']);

                            if ($now - $create_time > 24 * 60 * 60) { // 24 hours
                                $this->CI->UserModel->delete_active_code($mobo_id, $params['active_code']);
                                $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_ACCOUNT_FAIL');
                                return;
                            }

                            //$account_info = $this->CI->UserModel->get_user_by_phone($data_info['phone']);

                            $key_mobo_phone = 'MOBO_Phone_' . $params['phone'];
                            $account_info = $cache_user->store($key_mobo_phone, $this->CI->UserModel, 'get_user_by_phone', array($data_info['phone']));

                            if (empty($account_info) == TRUE) {
                                $result = $this->CI->UserModel->delete_active_code($mobo_id, $params['active_code']);
                                $params['password'] = md5($params['password']);
                                if ($result == TRUE) {
                                    $data_update = array(
                                        'password' => $params['password'],
                                        'phone' => $data_info['phone'],
                                        'temporary' => NULL,
                                        //'state' => NULL,
                                    );
                                    $result = $this->CI->UserModel->update_info($mobo_id, $data_update);
                                    if ($result == TRUE) {
                                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_ACCOUNT_SUCCESS');
                                    } else {
                                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_ACCOUNT_FAIL');
                                    }
                                } else {
                                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_ACCOUNT_FAIL');
                                }
                            } else {
                                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_EXIST');
                            }
                        } else {
                            $count = $count + 1;
                            $cache_system->save('ACTIVE_ACCOUNT_FAIL' . $access_token['mobo_id'] . $params['active_code'] . $params['device_id'], $count, 60 * 5);
                            $this->_response = new MEAPI_Response_APIResponse($request, 'ACTIVE_CODE_INVALID');
                        }
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_EXIST');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_account_trial(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');

            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');

                $result = $this->CI->UserModel->get_account_trial_by_device_id($params['device_id']);
                if (empty($result) == FALSE) {
                    $this->CI->load->helper('phone_helper');
                    $response = array();
                    foreach ($result as $key => $value) {
                        if (empty($result[$key]['temporary']) === FALSE) {
                            $result[$key]['temporary'] = show_phone($result[$key]['temporary']);
                            $response[] = $result[$key];
                        }
                    }
                    $this->_response = new MEAPI_Response_APIResponse($request, 'GET_ACCOUNT_TRIAL_SUCCESS', $response);
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_TRIAL_EMPTY');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function update_info(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                //$access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                $key_mobo_ac = 'MOBO_AC_' . md5($params['access_token']);
                $access_token = $cache_user->store($key_mobo_ac, $this->CI->UserModel, 'get_user_by_access_token', array($params['access_token']));

                if (empty($access_token) == FALSE) {
                    $group_mobo = 'MOBO_GROUP_AC_' . $access_token['mobo_id'];
                    $cache_user->append_key($key_mobo_ac, $group_mobo);

                    $mobo_id = $access_token['mobo_id'];

                    if (empty($params['fullname']) == FALSE) {
                        $data_update['fullname'] = $params['fullname'];
                    }

                    if (empty($params['gender']) == FALSE) {
                        $data_update['gender'] = str_replace(array('female', 'male', 'other'), array('2', '1', '3'), $params['gender']);
                    }

                    if (empty($params['birthday']) == FALSE) {
                        $data_update['birthday'] = $params['birthday'];
                    }

                    if (empty($_FILES['avatar']) == FALSE) {
                        $this->CI->load->MEAPI_Library('ImageLib');
                        $filepath = $_FILES['avatar']['tmp_name'];
                        //$filename = $_FILES['avatar']['name'];
                        $filename = md5($mobo_id);

                        $result_upload = $this->CI->ImageLib->upload($filepath, $filename, $mobo_id);
                        if ($result_upload != FALSE) {
                            $data_update['avatar'] = 1;
                        } else {
                            $this->_response = new MEAPI_Response_APIResponse($request, 'UPDATE_INFO_FAIL');
                            return;
                        }
                    }

                    $result = $this->CI->UserModel->update_info($mobo_id, $data_update);

                    if (empty($result) == FALSE) {
                        $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                        $cache_user->delete_group($group_mobo);

                        $group_mobo = 'MOBO_GROUP_AC' . $access_token['mobo_id'];
                        $cache_user->delete_group($group_mobo, $key_mobo_ac);

                        $this->_response = new MEAPI_Response_APIResponse($request, 'UPDATE_INFO_SUCCESS');
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'UPDATE_INFO_FAIL');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function get_info(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                //$access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);

                $this->CI->load->library('cache');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');

                $key_mobo_ac = 'MOBO_AC_' . md5($params['access_token']);
                $access_token = $cache_user->store($key_mobo_ac, $this->CI->UserModel, 'verify_access_token', array($params['access_token']));

                if (empty($access_token) == FALSE) {
                    $group_mobo = 'MOBO_GROUP_AC_' . $access_token['mobo_id'];
                    $cache_user->append_key($key_mobo_ac, $group_mobo);

                    $mobo_id = $access_token['mobo_id'];
                    $result = $this->CI->UserModel->get_info($mobo_id);

                    $key_mobo_info = 'MOBO_Info_' . $mobo_id;
                    $account_info = $cache_user->store($key_mobo_info, $this->CI->UserModel, 'get_info', array($mobo_id));

                    $group_mobo = 'MOBO_' . $account_info['mobo_id'];
                    $cache_user->append_key($key_mobo_info, $group_mobo);

                    if (empty($result) == FALSE) {
                        if (empty($result['avatar']) == FALSE) {
                            $this->CI->load->MEAPI_Helper('transaction');
                            $filename = md5(md5($mobo_id)) . '.png';
                            $result['avatar'] = base_url('assets/upload' . '/' . gen_user_photo_path($result['mobo_id']) . '/' . $filename);
                        } else {
                            $result['avatar'] = '';
                        }
                        $response = array(
                            'mobo_id' => $result['mobo_id'],
                            'fullname' => $result['fullname'],
                            'birthday' => $result['birthday'],
                            'gender' => str_replace(array('1', '2', '3'), array('male', 'female', 'other'), $result['gender']),
                            'avatar' => $result['avatar'],
                            'facebook_id' => $result['facebook_id']
                        );
                        if (SERVICE_ID == 100) {
                            $response['phone'] = $result['phone'] ? $result['phone'] : NULL;
                        }
                        $this->_response = new MEAPI_Response_APIResponse($request, 'GET_INFO_SUCCESS', $response);
                    } else {
                        $this->_response = new MEAPI_Response_APIResponse($request, 'GET_INFO_FAIL');
                    }
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function add_service(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'fullname', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Model('UserModel');
            $access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
            if (empty($access_token) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                return;
            }

            $data_insert = array(
                'mobo_id' => $access_token['mobo_id'],
                'mobo_service_id' => rand(),
                'fullname' => $params['fullname'],
                'device_id' => $params['device_id'],
                'channel' => $params['channel']
            );
            $service_id = intval($access_token['service_id']);
            $insert_id = $this->CI->UserModel->insert_service($service_id, $data_insert);
            if (empty($insert_id) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'ADD_SERVICE_SUCCESS');
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'ADD_SERVICE_FAIL');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function list_service(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }

            $this->CI->load->MEAPI_Model('UserModel');
            $access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
            if (empty($access_token) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                return;
            }

            //ChinhLD
            //Get list service from account_service_1
            $service_id = $access_token['service_id'];
            $list_service = $this->CI->UserModel->get_service_by_mobo_id($service_id, $access_token['mobo_id']);

            if (empty($list_service) == FALSE) {
                //convert string
                $token_key = MEAPI_Config_Token::getTokenKey();
                $response = array();
                $this->CI->load->library('Crypt');
                foreach ($list_service as $key => $value) {
                    $key = json_encode($value);
                    $item_service = array(
                        'fullname' => $value['fullname'],
                        'mobo_id' => $value['mobo_id'],
                        'mobo_service_id' => $value['mobo_service_id'],
                        'token_login' => base64_encode($this->CI->crypt->Encrypt($key, $token_key)),
                    );
                    $response[] = $item_service;
                }
                $this->_response = new MEAPI_Response_APIResponse($request, 'GET_LIST_SERVICE_SUCCESS', $response);
            } else
                $this->_response = new MEAPI_Response_APIResponse($request, 'GET_LIST_SERVICE_SUCCESS');
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function store_device_token(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'device_token', 'package_name', 'env', 'user_agent', 'channel', 'platform', 'telco', 'version', 'info'
            , 'devicemodel', 'lang', 'os', 'osversion', 'app');
            //info: serverid, charlevel,gender
            if (is_required($params, $needle) == FALSE) {
                $diff = array_diff($needle, array_keys($params));
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS', $diff);
            }
            $this->CI->load->MEAPI_Model('UserModel');
            $access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);
            if (empty($access_token) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCESS_TOKEN_INVALID');
                return;
            }
            $params['mobo_id'] = $access_token['mobo_id'];

            $this->CI->load->MEAPI_Library('StoreDevice');
            $response = $this->CI->StoreDevice->store($params);
            if ($response['status'] === FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'STORE_DEVICE_TOKEN_FAIL');
                return;
            }
            $this->_response = new MEAPI_Response_APIResponse($request, 'STORE_DEVICE_TOKEN_SUCCESS');
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function logout(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                $this->CI->load->MEAPI_Model('UserModel');
                $cache_user = $this->CI->cache->load('memcache', 'user_info');
                //$access_token = $this->CI->UserModel->get_user_by_access_token($params['access_token']);

                $key_mobo_ac = 'MOBO_AC_' . md5($params['access_token']);
                $access_token = $cache_user->store($key_mobo_ac, $this->CI->UserModel, 'get_user_by_access_token', array($params['access_token']));

                $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                $cache_user->append_key($key_mobo_ac, $group_mobo);
                if (empty($access_token) == TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'LOGOUT_SUCCESS');
                    return;
                }
                $result = $this->CI->UserModel->delete_access_token_by_mobo_id($access_token['mobo_id']);
                if ($result == TRUE) {
                    $group_mobo = 'MOBO_' . $access_token['mobo_id'];
                    $cache_user->delete_group($group_mobo);

                    $group_mobo = 'MOBO_GROUP_AC_' . $access_token['mobo_id'];
                    $cache_user->delete_group($group_mobo);

                    $this->CI->load->MEAPI_Library('InsightAPI');
                    $params['mobo_id'] = $access_token['mobo_id'];
                    $params['mobo_service_id'] = $access_token['mobo_service_id'];
                    $this->CI->InsightAPI->logout($params);

                    $this->_response = new MEAPI_Response_APIResponse($request, 'LOGOUT_SUCCESS');
                } else {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'LOGOUT_FAIL');
                }
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function create_active_code(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            if (empty($params['phone']) == TRUE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
                return;
            }
            $this->CI->load->helper('phone_helper');
            $params['phone'] = format_phone($params['phone']);

            $this->CI->load->MEAPI_Model('UserModel');
            $this->CI->load->MEAPI_Library('MoboUser');
            $user_info = $this->CI->UserModel->get_user_by_phone($params['phone']);

            if (empty($user_info) == FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'ACCOUNT_EXIST');
            } else {
                $active_code = $this->CI->MoboUser->gen_active_code();
                $data = array(
                    'mobo_id' => $user_info['mobo_id'],
                    'code' => $active_code,
                    'phone' => $params['phone'],
                );
                $result = $this->CI->UserModel->insert_active_code($data);
                if (empty($result) == TRUE) {
                    $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_ACTIVE_FAIL');
                } else {
                    $response = array(
                        'active_code' => $active_code,
                    );
                    $this->_response = new MEAPI_Response_APIResponse($request, 'SEND_CODE_ACTIVE_SUCCESS', $response);
                }
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

    public function ntp(MEAPI_RequestInterface $request) {
        $this->_response = new MEAPI_Response_APIResponse($request, 'NTP', array('timestamps' => time()));
    }

    private function sendActiveCode($mobo_id, $phone) {
        $this->CI->load->MEAPI_Model('UserModel');
        $this->CI->load->MEAPI_Library('MoboUser');
        $active_code = $this->CI->MoboUser->gen_active_code();
        $data_insert = array(
            'mobo_id' => $mobo_id,
            'phone' => $phone,
            'code' => $active_code,
        );
        $result = $this->CI->UserModel->insert_active_code($data_insert);

        if (empty($result) == FALSE) {
            $this->CI->load->MEAPI_Library('GatewayAPI');
            $message = MEAPI_Config_Message::getMessageActiveCode($active_code);
            $service = '7065';
            $result = $this->CI->GatewayAPI->send_mt($phone, $service, $message);
        }
        return $result == TRUE ? TRUE : FALSE;
    }

    public function clean_cache() {
        $this->CI->load->library('cache');
        $cache_system = $this->CI->cache->load('memcache', 'system_info');
        $cache_system->clean();
        $cache_userinfo = $this->CI->cache->load('memcache', 'user_info');
        $cache_userinfo->clean();
        echo 'OK';
        die;
    }

}
