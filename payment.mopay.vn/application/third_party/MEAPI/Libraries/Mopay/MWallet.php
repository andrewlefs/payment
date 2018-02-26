<?php

class MWallet {

    /**
     * @var CI_Controller
     */
    private $CI;
    private $url;
    private $secret_key;
    private $app;

    public function __construct() {
        $this->CI = &get_instance();
        $config = MEAPI_Config_Mopay::wallet();
        $this->url = $config['url'];
        $this->app = $config['app'];
        $this->secret_key = $config['secret'];
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function deposit($mobo_id, $blackbox_transaction) {
        $args = array(
            'mobo_id' => $mobo_id,
            'blackbox_transaction' => $blackbox_transaction
        );
        $result = $this->_call_api('wallet', 'deposit', $args);
        if (empty($result) === FALSE && $result['code'] == 110) {
            return array(
                'code' => 1,
                'detail' => array(
                    'credit' => $result['data']['credit'],
                    'money' => $result['data']['money'],
                    'balance' => $result['data']['balance']
                )
            );
        } else {
            return array(
                'code' => 0,
                'detail' => $result['data']
            );
        }
    }

    public function withdraw($mobo_id, $blackbox_transaction) {
        $args = array(
            'mobo_id' => $mobo_id,
            'blackbox_transaction' => $blackbox_transaction
        );
        $result = $this->_call_api('wallet', 'withdraw', $args);
        if (empty($result) === FALSE && $result['code'] == 110) {
            return array(
                'code' => 1,
                'detail' => array(
                    'credit' => $result['data']['credit'],
                    'balance' => $result['data']['balance']
                )
            );
        } elseif (empty($result) === FALSE && $result['code'] == 103) {
            return array(
                'code' => -1,
            );
        } else {
            return array(
                'code' => 0
            );
        }
    }

    public function balance($mobo_id) {
        $args = array(
            'mobo_id' => $mobo_id
        );
        $result = $this->_call_api('wallet', 'balance', $args);
        if (empty($result) === FALSE && $result['code'] == 110) {
            return array(
                'code' => 1,
                'detail' => array(
                    'balance' => $result['data']['balance'],
                    'last_update' => $result['data']['last_update']
                )
            );
        } else {
            return array(
                'code' => 0
            );
        }
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