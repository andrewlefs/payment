<?php

class SMS_9029 {

    private $_params;
    private $_partner_id = '1025';
    private $_private_key = 'bdad6d0a-a575-11e2-99ff-5cf3fc4b0814';
    private $_connection_id = 2;
    private $_mt;

    public function __construct() {

    }

    public function set_params($params) {
        if (empty($params['contentid']) === FALSE) {
            $t = explode(' ', $params['contentid']);
            $parse = explode('|', $t[0]);
            $content = $params['syntax'] . ' ' . $params['gamecode'] . ' ' . $parse[0] . ' ' . $params['account'] . ' ' . $parse[1] . ' ' . $t[1];
            $params['_request'] = $params['contentid'];
            $params['request'] = $content;
            $params['private_key'] = $params['account'] . $params['result'] . $params['motime'] . $this->_private_key;
            $params['_telco'] = $params['telco'];
            $params['_amount'] = $params['totalamount'];
        } elseif (empty($params['params']) === FALSE) {
            $params['phone'] = $params['msisdn'];
            $params['moid'] = $params['sessionid'];
            $params['tokenkey'] = $params['key'];
            $params['_request'] = $params['request'] = $params['params'];
            $params['_amount'] = $params['amount'];
            $params['private_key'] = $params['telco'] . '@9029contentRequest';
        }
        $this->_params = $params;
    }

    public function verify_token() {
        if (ENVIRONMENT == 'development') {
            return TRUE;
        }
        $arrToken = array(
            $this->_params['moid'],
            $this->_params['partnerid'],
            $this->_params['_telco'],
            $this->_params['servicenum'],
            $this->_params['phone'],
            $this->_params['syntax'],
            $this->_params['_request'],
            $this->_params['gamecode'],
            $this->_params['_amount'],
            $this->_params['private_key']
        );

        $token = md5(implode('', $arrToken));
        if ($token == $this->_params['tokenkey'])
            return TRUE;
        return FALSE;
    }

    public function get_data() {
        $sms = array(
            'mo_id' => $this->_params['moid'],
            'partner_id' => $this->_params['partnerid'] ? $this->_params['partnerid'] : $this->_partner_id,
            'telco' => $this->_params['telco'], //mobi,vina,viettel,gtel,vietnamobile
            'service_number' => 9029,
            'phone' => $this->_params['phone'],
            'code' => 'ME',
            'content' => $this->_params['request'],
            'receive_time' => date('Y-m-d H:i:s', $this->_params['receivedtime'] ? strtotime($this->_params['receivedtime']) : time()),
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
        $result['order_id'] = $sms_content[5];
        $result['mobo_id'] = $sms_content[4];
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
        $this->_mt = $mt;
        $objReceived->update_mt($this->get_connection_id(), $this->get_mo_id(), $mt, $cdr);

    }

    public function get_price($service_num='') {
        return $this->_params['_amount'];
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
        //echo 0;
		echo -2;
        if ($this->_mt) {
            echo '|' . $this->_mt;
            $this->_mt = NULL;
        }
        exit;
    }

    public function response_success() {
        echo 0;
        if ($this->_mt) {
            echo '|' . $this->_mt;
            $this->_mt = NULL;
        }
        exit;
    }

}
