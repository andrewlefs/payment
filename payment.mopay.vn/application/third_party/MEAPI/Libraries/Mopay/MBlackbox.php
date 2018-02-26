<?php

class MBlackbox {

    /**
     * @var CI_Controller
     */
    private $CI;
    private $url;
    private $secret_key;
    private $app;

    public function __construct() {
        $this->CI = &get_instance();
        $config = MEAPI_Config_Mopay::blackbox();
        $this->url = $config['url'];
        $this->app = $config['app'];
        $this->secret_key = $config['secret'];
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function exchange($exchange_transacion, $mobo_id, $scope_id, $vnd, $type, $subtype = NULL) {
        $type = str_replace('banking', 'bank', $type);
        $args = array(
            'transid' => $exchange_transacion,
            'mobo_id' => $mobo_id,
            'service_id' => $scope_id,
            'vnd' => $vnd,
            'type' => $type,
            'subtype' => $subtype,
        );
        $result = $this->_call_api('bbdeposit', 'getexchange', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 180000) {
                return array(
                    'code' => 1,
                    'credit' => $result['data']['mcoin']
                );
            } else {
                return array(
                    'code' => -1,
                    'detail' => $result
                );
            }
            return $response;
        }
        return FALSE;
    }

    public function get_transaction_deposit($transacion_id) {
        $args = array(
            'bb_transid' => $transacion_id
        );
        $result = $this->_call_api('bbdeposit', 'verify', $args);

        if (is_array($result) == TRUE) {
            if ($result['code'] == 110000) {
                return array(
                    'code' => 1,
                    'credit' => $result['data']['mcoin'],
                    'money' => $result['data']['vnd']
                );
            }
        }
        return array(
            'code' => -1,
            'detail' => $result
        );
    }

    public function get_transaction_withdraw($transacion_id) {
        $args = array(
            'bb_transid' => $transacion_id,
            'item' => '1',
            'service_id' => '1'
        );
        $result = $this->_call_api('bbwithdraw', 'verify', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 130000) {
                return array(
                    'code' => 1,
                    'credit' => $result['data']['mcoin']
                );
            }
        }
        return array(
            'code' => -1,
            'detail' => $result
        );
    }

    public function take_transaction_deposit($deposit_transacion, $mobo_id, $scope_id, $vnd, $type, $subtype = NULL) {
        $type = str_replace('banking', 'bank', $type);
        $args = array(
            'transid' => $deposit_transacion,
            'mobo_id' => $mobo_id,
            'service_id' => $scope_id,
            'vnd' => $vnd,
            'type' => $type,
            'subtype' => $subtype,
        );
        $result = $this->_call_api('bbdeposit', 'getkey', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 100000) {
                return array(
                    'code' => 1,
                    'blackbox_transaction' => $result['data']['bb_transid']
                );
            } else {
                return array(
                    'code' => -1,
                    'detail' => $result
                );
            }
            return $response;
        }
        return FALSE;
    }

    public function take_transaction_withdraw($withdraw_transacion, $mobo_id, $scope_id, $credit) {
        $args = array(
            'transid' => $withdraw_transacion,
            'mobo_id' => $mobo_id,
            'service_id' => $scope_id,
            'mcoin' => $credit
        );
        $result = $this->_call_api('bbwithdraw', 'getkey', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 120000) {
                return array(
                    'code' => 1,
                    'blackbox_transaction' => $result['data']['bb_transid']
                );
            } else {
                return array(
                    'code' => -1,
                    'detail' => $result
                );
            }
            return $response;
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