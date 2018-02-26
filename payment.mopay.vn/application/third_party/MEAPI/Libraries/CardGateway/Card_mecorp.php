<?php

/**
 * Description of CardGate
 *
 * @author thainpv
 */
class Card_mecorp
{

    private $CI;
    private $_params = array();
    private $_card_url = 'http://123.30.133.183:7777/PAYMENT/CardServlet'; //CardServlet
    private $_partner_key = '5361f20d-34d5-11e5-992e-5cf3fc4a';
    private $_partner_id = '1025';
    private $_company_code = '[MECORP]';
    private $_service_code = '002002';
    private $_card_connection_id = 1;

    public function __construct()
    {
        if (ENVIRONMENT == 'development') {
            $this->_gateway = 'http://123.30.133.183:7777/PAYMENT/CardServlet';
        }
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_library('curl');
    }

    public function set_params($params)
    {
        $this->_params = $params;
    }

    /*
     * @input: array('cardType','transactionID','cardSerial','cardPIN','userName')
     * @output: array('status','code','msg','money')
     */

    public function process()
    {
        $params = $this->_params;
        $params['partnerID'] = $this->_partner_id;
        $params['companyCode'] = $this->_company_code;
        $params['serviceCode'] = ( $params['service_code'] ? $params['service_code'] : $this->_service_code );
        $params['gameCode'] = 'mopay'.$params['service_id'];

        $needle = array('cardType', 'partnerID', 'transactionID', 'cardSerial', 'cardPIN', 'userName', 'companyCode', 'serviceCode', 'gameCode');
        $records = make_array($params, $needle);

        $records['cardType'] = str_replace('vms', 'mobi', $records['cardType']);

        $records['signature'] = make_signature($records, $this->_partner_key);
        $url = $this->_card_url . '?' . http_build_query($records);

        if ($records['cardType'] == 'mobi' && $records['cardSerial'] == '123456789012' && $records['cardPin'] = '123456789012' && 1 == 2) {
            $response = '00|nap thanh cong|50000';
        } else {
            $response = $this->CI->curl->get($url);
        }
        if ($response) {
            //'00|nap thanh cong|10000';
            $a_response = explode('|', $response);
            if ($a_response[0] === '00') {
                $result = array(
                    'code' => 1,
                    'msg' => $a_response[1],
                    'money' => $a_response[2]
                );
            } else {
                $result = array(
                    'code' => 0,
                    'msg' => $a_response[1],
                    'money' => $a_response[2]
                );
                if(in_array($a_response[0],array('21','201','301','401'))){
                    $result['msg'] = 'Thẻ đã sử dụng';
                }elseif(in_array($a_response[0],array('202','308','402'))){
                    $result['msg'] = 'Thẻ đã bị khoá';
                }elseif(in_array($a_response[0],array('203','307','403'))){
                    $result['msg'] = 'Thẻ đã hết hạn';
                }elseif(in_array($a_response[0],array('212','302','412'))){
                    $result['msg'] = 'Thẻ không tồn tại';
                }elseif(in_array($a_response[0],array('04','210','304','305','410'))){
                    $result['msg'] = 'Mã thẻ không đúng định dạng';
                }elseif(in_array($a_response[0],array('204','308','414'))){
                    $result['msg'] = 'Thẻ chưa được kích hoạt';
                }else{
                    $result['msg'] = 'Thông tin thẻ không chính xác';
                }
            }
        } else {
            $result = array(
                'code' => -1,
                'msg' => '',
                'money' => 0
            );
        }
        return $result;
    }

    public function get_connection_id()
    {
        return $this->_card_connection_id;
    }

}
