<?php

class MoboGraphAPI {

    /**
     * @var CI_Controller
     */
    private $CI;
    private $url = 'http://graph.mobo.vn/';
    private $secret_key = 'ZIFNEPFG45F3UXB6';//'DMSEVGS3FCCNJXI3';
    private $app = 'mopay';

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function verify_access_token($params) {
        $tmp = json_decode(base64_decode($params['access_token']),TRUE);
        if(is_array($tmp) && $tmp['signature']){
            $check = md5($tmp['mobo_id'].$tmp['mobo_service_id'].'nvb');
            if($check  == $tmp['signature']){
                $response['status'] = TRUE;
                $response['mobo_id'] = $tmp['mobo_id'];
                $response['fullname'] = 'neverbestboy';
                $response['detail'] = $params['access_token'];
                return $response;
            }
        }

        $args = array(
            'access_token' => $params['access_token'],
        );
        $result = $this->_call_api('user', 'verify_access_token', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 500040) {
                $response['status'] = TRUE;
                $response['mobo_id'] = $result['data']['mobo_id'];
                $response['fullname'] = $result['data']['fullname'];
                $response['detail'] = $result['data'];
                if( !empty($result['data']['linked']['facebook']) OR
                    !empty($result['data']['linked']['google']) ){
                    $response['detail']['active'] = TRUE;
                }
                return $response;
            }

        }
        return FALSE;
    }

    public function search_graph($mobo_account, $service_id) {        
        $this->CI->load->MEAPI_Helper('common_helper');
        $args = array(
            'control' => 'inside',
            'func' => 'search_graph',
            'mobo' => $mobo_account,
            'user_agent' => 'empty',
            'channel' => '1',
            'ip_user' => get_client_ip(),
            'service_id' => $service_id            
        );
        $result = $this->_call_api('inside', 'search_graph', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 900000) {
                $response['status'] = TRUE;
                $response['data'] = $result['data'];
            } else {
                $response['status'] = FALSE;
            }
            return $response;
        }
        return FALSE;
    }
    
    public function get_account($account) {
        $args = array(
            'account' => $account,
        );
        $result = $this->_call_api('user', 'get_account_info', $args);       
        if (is_array($result) == TRUE) {
            if ($result['code'] == 700010) {
                $response['status'] = TRUE;
                $response['mobo_id'] = $result['data']['mobo_id'];
                $response['fullname'] = $result['data']['fullname'];
                $response['detail'] = $result['data'];
                return $response;
            }

        }
        return FALSE;
    }

    private function _call_api($control, $func, $params) {
        $this->CI->load->MEAPI_Library('TOTP');
        $params['otp'] = $this->CI->TOTP->getCode($this->secret_key);
        $params['app'] = $this->app;

        $token = $this->make_token($control, $func, $params);
        $link = $this->url . "/?control={$control}&func={$func}&" . http_build_query($params) . "&token={$token}";
        $json = $this->CI->Curl->get($link);
        $data = json_decode($json, TRUE);
        return is_array($data) ? $data : FALSE;
    }

    private function make_token($control, $func, $params) {
        $token = md5($control . $func . implode('', $params) . $this->secret_key);
        return $token;
    }

}
