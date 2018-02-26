<?php

class SMS_7x65 {

    private $_params;
    private $_private_key = 'd1c11441bcc06058163f6c07ebcbbc89';
    private $_connection_id = 1;
    private $_gateway = 'http://123.30.133.183:7777/SMS_API_Outside/Send_MT';
    private $CI;
    private $_mt_content = "Chao tai khoan username, ban vua nap thanh cong total MCOINS!";
    private $_mt_fail = "tin nhan sai cu phap";
    private $_wap_title = "";

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('curl'); 
    }
    
    public function set_params($params) {
        $this->_params = $params;
    }

    public function verify_token() {
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
            'moid' => $this->_params['moid'],
            'partner_id' => $this->_params['partnerid'],
            'telco' => $this->_params['telco'], //mobi,vina,viettel,gtel,vietnamobile
            'service_number' => $this->_params['servicenum'],
            'phone' => $this->_params['phone'],
            'code' => $this->_params['syntax'],
            'content' => $this->_params['request'],
            'receive_time' => date('Y-m-d H:i:s', strtotime($this->_params['receivedtime'])),
            'connection_id' => $this->_connection_id
        );
        if(is_required($sms, array('moid','partner_id','telco','service_number','phone','code','content','receive_time'))){
            return $sms;
        }else{
            return FALSE;
        }
    }
    
    public function parse_mo(){
        $sms_content = explode(' ',$this->_params['request']);        
        $result['order_id'] = intval($sms_content[2]);
        $result['mobo_id'] = $sms_content[1];
        if(empty($result['order_id']) !== TRUE && empty($result['mobo_id']) !== TRUE){
            return $result;
        }else{
            return FALSE;
        }
    }
    
    public function send_mt($sms,$cdr=1){        
        $mt_url = $this->_gateway;
        $find = array('username','total');
        $replace = array($sms['mobo_id'],$sms['credit']);        
        $mt = urlencode($cdr===1?str_replace($find,$replace,$this->_mt_content):$this->_mt_fail); 
        $mo_id = $sms['moid'];
        $telco = $sms['telco'];
        $service_number = $sms['service_number'];
        $phone = $sms['phone'];
        $syntax = urlencode($sms['code']);
        $request = urlencode($sms['content']);
        $waptitle = $this->_wap_title;
        $msgtype = 0;
        $partner_id = $sms['partner_id'];
        $request_id = $mo_id;
        $chargin = $cdr;
        $private_key = $this->_private_key;
        //create token
        $token = md5($mo_id.$telco.$service_number.$phone.$syntax.$request.$waptitle.$mt.$msgtype.$chargin.$partner_id.$request_id.$private_key);
        $url    = "$mt_url?MOId=$mo_id&Telco=$telco&ServiceNum=$service_number&Phone=$phone&Syntax=$syntax&Request=$request&WAPTitle=$waptitle&Response=$mt&MsgType=$msgtype&Charging=$chargin&PartnerID=$partner_id&RequestID=$request_id&TokenKey=$token";        
        
        $response = $this->CI->curl->get($url);    
        $fields = array(
            'url' => $url,
            'response' => $response
        ); 
        MEAPI_Log::writeCsv($fields, 'mt_log');   
        return $response;
        //if($response)return TRUE;
        //return FALSE;
        
    }
    
    public function get_price($service_num){
        switch ($service_num){
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
            default:
            case '7765':
                $price = 15000;
                break;
        }
        return $price;
    }
    
    public function get_connection(){
        return $this->_connection_id;
    }

}
