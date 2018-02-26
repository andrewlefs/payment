<?php

class Vcb_mecorp {

    private $CI;
    private $secret_key = 'G4XE24UPRVPHIV5R';
    private $url = 'http://apivcb.mopay.vn/vietcombank';

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function deposit($params){
        $data = array(
            'PartnerId' => 'ME',
            'UserId' => strval($params['phone']),//'0909000200',//strval($params['mobo_id']),
            'TxnCode' => '8001',
            'TxnAmount' => $params['money'],
            'TxnCurrency' => 'VND',
            'TxnId' => $params['transaction_id'],
            'TxnDesc' => $params['mobo_id'] . ' rút tiền',
            'TxnStatus' => '0',
        );
        $result = $this->_call_api('cash_out',$data);
        if($result['Code'] == 0 && isset($result['Code']) == TRUE){
            $response['status'] = 1;
            $response['transaction_id'] = $result['Data'];
            $response['message'] = $result['Message'];
        }else{
            $response['status'] = 0;
            $response['message'] = $result['Message'];
        }
        return $response;
    }

    public function withdraw($params){
        $data = array(
            'PartnerId' => 'ME',
            'UserId' => strval($params['phone']),//'0909000200',//strval($params['mobo_id']),
            'TxnCode' => '8001',
            'TxnAmount' => $params['money'],
            'TxnCurrency' => 'VND',
            'TxnId' => $params['transaction_id'],
            'TxnDesc' => $params['mobo_id'] . ' rút tiền',
            'TxnStatus' => '0',
        );
        $result = $this->_call_api('cash_in',$data);
        //print_r($data);
        //print_r($result);exit;
        if($result['Code'] == 0 && isset($result['Code']) == TRUE){
            $response['status'] = 1;
            $response['transaction_id'] = $result['Data'];
            $response['message'] = $result['Message'];
        }else{
            $response['status'] = 0;
            $response['message'] = $result['Message'];
        }
        return $response;
    }

    private function _call_api($method, $params) {

        $paramJsonString = json_encode($params);
        $paramBase64 = base64_encode($paramJsonString);
        $token = $this->make_token($method, $paramBase64);

        $link = $this->url . "/?method={$method}&data=".urlencode($paramBase64)."&token={$token}";//echo $link;
        $json = $this->CI->Curl->get($link);
        $data = json_decode($json, TRUE);
        return is_array($data) ? $data['Data'] : FALSE;
    }

    private function make_token($method, $params) {
        $token = md5($method . $params . $this->secret_key);
        return $token;
    }




}
