<?php

/**
 * Created by PhpStorm.
 * User: thainpv
 * Date: 11/7/2016
 * Time: 10:19 AM
 */
class MomoService
{
    private $CI;
    //private $url = 'https://payment.momo.vn:18080/gw_payment/query_status';//sandbox: 18081, product: 18080
    private $url = 'https://payment.momo.vn/gw_payment/query_status';//sandbox: 18081, product: 18080
    private $access_key = 'QjCAp4B1bAQqR5xu';
    private $partner_code = 'ME112016';
    private $secret_key = '3OhDthOTeDTfIU4YUQesmPsp4CNavhb';
    private $_params = array();

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function set_params($params) {
        $this->_params = $params;
    }

    public function verify() {
        $params = $this->_params;
        return $this->_call_api($params);
    }

    private function _call_api($params) {
        $args = array(
            'partner_code' => $this->partner_code,
            'access_key' => $this->access_key,
            'transaction_id' => $params['transaction_id'],
            'order_id' => $params['order_id']
        );
        $signature = $this->make_signature($args);
        $request_uri = http_build_query($args).'&signature='.$signature;
        $data = $this->CI->Curl->post($this->url, $request_uri);
        $data = json_decode($data, TRUE);
        if (!empty($data['transaction_id'])) {
            return $data;
        } else {
            return FALSE;
        }
    }

    private function make_signature($params) {
        $token =  hash_hmac('sha256',http_build_query($params),$this->secret_key);//md5($this->partner_code.''.$this->access_key.''.implode('', $params));
        return $token;
    }
}