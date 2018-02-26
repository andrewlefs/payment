<?php

class SMS_7x65 {

    private $_params;
    private $_private_key = 'd1c11441bcc06058163f6c07ebcbbc89';
    private $_connection_id = 1;
    private $_gateway = 'http://192.168.11.5:7777/SMS_API_Outside/Send_MT';
    private $CI;
    private $_wap_title = "";

    public function __construct() {
        if (ENVIRONMENT == 'development') {
            $this->_gateway = 'http://123.30.133.183:7777/SMS_API_Outside/Send_MT';
        }
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function set_params($params) {
        $this->_params = $params;
    }

    public function verify_token() {
        return TRUE;
        $arrToken = array(
            $this->_params['moid'],
            $this->_params['partnerid'],
            $this->_params['telco'],
            $this->_params['servicenum'],
            $this->_params['phone'],
            $this->_params['syntax'],
            $this->_params['request'],
            $this->_params['receivedtime'],
            $this->_private_key
        );
        $token = md5(implode('', $arrToken));
        if ($token == $this->_params['tokenkey'])
            return TRUE;
        return FALSE;
    }

    public function get_data() {
        $sms = array(
            'mo_id' => $this->_params['moid'],
            'partner_id' => $this->_params['partnerid'],
            'telco' => $this->_params['telco'], //mobi,vina,viettel,gtel,vietnamobile
            'service_number' => $this->_params['servicenum'],
            'phone' => $this->_params['phone'],
            'code' => $this->_params['syntax'],
            'content' => $this->_params['request'],
            'receive_time' => date('Y-m-d H:i:s', strtotime($this->_params['receivedtime'])),
            'connection_id' => $this->_connection_id
        );
        if (is_required($sms, array('mo_id', 'partner_id', 'telco', 'service_number', 'phone', 'code', 'content', 'receive_time'))) {
            return $sms;
        } else {
            return FALSE;
        }
    }

    public function parse_mo() {
        $sms_content = explode(' ', $this->_params['request']);
        $result['order_id'] = $sms_content[2];
        $result['mobo_id'] = $sms_content[1];
        if (empty($result['order_id']) !== TRUE && empty($result['mobo_id']) !== TRUE) {
            return $result;
        } else {
            return FALSE;
        }
    }

    public function get_sms_transaction_id() {
        $data = $this->parse_mo();
        if (is_array($data)) {
            return $data['order_id'];
        }
    }

    public function get_mobo_id() {
        $data = $this->parse_mo();
        if (is_array($data)) {
            return $data['mobo_id'];
        }
    }

    public function send_mt($mt, $cdr = 1, $sms, $objReceived) {
        if (ENVIRONMENT == 'development') {
            echo $mt;
            $objReceived->update_mt($this->get_connection_id(), $this->get_mo_id(), $mt, $cdr);
            //exit;
        }
        $mo_id = $sms['mo_id'];
        $telco = $sms['telco'];
        $service_number = $sms['service_number'];
        $phone = $sms['phone'];
        $syntax = urlencode($sms['code']);
        $request = urlencode($sms['content']);
        $msgtype = 0;
        $partner_id = $sms['partner_id'];
        $request_id = $mo_id;
        $chargin = $cdr;
        $mt_url = $this->_gateway;
        $waptitle = $this->_wap_title;
        $private_key = $this->_private_key;
        //create token
        $token = md5($mo_id . $telco . $service_number . $phone . $syntax . $request . $waptitle . $mt . $msgtype . $chargin . $partner_id . $request_id . $private_key);
        $url = "$mt_url?MOId=$mo_id&Telco=$telco&ServiceNum=$service_number&Phone=$phone&Syntax=$syntax&Request=$request&WAPTitle=$waptitle&Response=" . urlencode($mt) . "&MsgType=$msgtype&Charging=$chargin&PartnerID=$partner_id&RequestID=$request_id&TokenKey=$token";
        $objReceived->update_mt($this->get_connection_id(), $this->get_mo_id(), $mt, $cdr);
        $response = $this->CI->Curl->get($url);
        return $response;
    }

    public function get_price($service_num) {
        $price = 0;
        switch ($service_num) {
            case '7065':
                $price = 500;
                break;
            case '7165':
                $price = 1000;
                break;
            case '7265':
                $price = 2000;
                break;
            case '7365':
                $price = 3000;
                break;
            case '7465':
                $price = 4000;
                break;
            case '7565':
                $price = 5000;
                break;
            case '7665':
                $price = 10000;
                break;
            case '7765':
                $price = 15000;
                break;
        }
        return $price;
    }

    public function get_connection_id() {
        return $this->_connection_id;
    }

    public function get_mo_id() {
        return $this->_params['moid'];
    }

    public function response_invalid_token() {
        echo -1;
        exit;
    }

    public function response_invalid_data() {
        echo -99;
        exit;
    }

    public function response_fail($error_detail = NULL) {
        if (ENVIRONMENT == 'development') {
            print_r($error_detail);
        }
        echo -2;
        exit;
    }

    public function response_success() {
        echo 0;
        exit;
    }

}
