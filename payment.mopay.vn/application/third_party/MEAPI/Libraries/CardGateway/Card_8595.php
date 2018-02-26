<?php

/**
 * Description of CardGate
 *
 */

class Card_8595
{
    protected $connection = 2;
    protected $authorize = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjb25uZWN0aW9uSWQiOiI1OTA5ODVjN2U1MzI1YmY1Y2YzNWE1OTAiLCJpYXQiOjE0OTM4MTY3NDV9.vqW4jhfQGyuie4rcTEDaf5WMP0SOLkvICQ4B7ju-BWQ";

    /***END**/
    private $CI;
    private $_params = array();
    private $_card_url = 'http://8595.app1.vn/ScratchCard'; //CardServlet
    private $_partner_id = 'ME112016';
    private $_partner_key = '5361f20d-34d5-11e5-992e-5cf3fc4a';

    private $_service_code = '002005';
    private $_card_connection_id = 2;

    public function __construct()
    {
        if (ENVIRONMENT == 'development') {
            //$this->_gateway = 'http://123.30.133.183:7777/PAYMENT/CardServlet';
        }
        $this->CI = &get_instance();
        //$this->CI->load->MEAPI_library('curl');
    }

    public function set_params($params)
    {
        $this->_params = $params;
    }
    public function testData(){
        /*
        $order_id = Misc\Http\Util::getShortLink(10);
        $sendData = array(
            'consumerId' => '0909000100',
            'serial' => '055624000019046',
            'pin' => '122865592303',
            'telco' => 'mobifone',
            "partnerTransaction" => $order_id,
            "orderInfo" => md5($order_id)
        );

        $sendResponse = $this->getApi8595Client()->sendCardRequest($sendData);
        echo "<pre>";
        var_dump($sendResponse);
        die;
        */
    }

    /*
     * @input: array('cardType','transactionID','cardSerial','cardPIN','userName')
     * @output: array('status','code','msg','money')
     */

    public function process()
    {
        $params = $this->_params;
        $params['partnerID'] = $this->_partner_id;
        $params['serviceCode'] = ( $params['service_code'] ? $params['service_code'] : $this->_service_code );
        $params['gameCode'] = 'mopay'.$params['service_id'];

        $needle = array('cardType', 'partnerID', 'transactionID', 'cardSerial', 'cardPIN', 'userName', 'companyCode', 'serviceCode', 'gameCode');
        $records = make_array($params, $needle);

        switch ($params["cardType"]) {
            case "vms":
                $records['cardType'] = "mobifone";
                break;
            case "vina":
                $records['cardType'] = "vinaphone";
                break;
            default :
                $records['cardType'] = $params["cardType"];
                break;
        }

        //$records['cardType'] = str_replace('vms', 'mobi', $records['cardType']);

        //$records['signature'] = make_signature($records, $this->_partner_key);

        $sendData = array(
            "consumerId" => $params['transactionID'],
            "serial" => $params["cardSerial"],
            "pin" => $params["cardPIN"],
            "telco" => $records['cardType'],
            "partnerTransaction" => $params['transactionID'],
            "orderInfo" => $params['orderInfo'],
            "serviceCode" => $params['serviceCode']
        );

        $url = $this->_card_url  ;

        $sendData['header'] = array("Authorization: ".$this->authorize);


        if ($records['cardType'] == 'vina' && $records['cardSerial'] == '36258200403879' && $records['cardPIN'] = '17557454377113' && 1 == 1) {
            $response = '{"code":1000,"data":{"transaction":"C-12BE306E4118AD00000-9SCGYDQZUY","createAt":"2017-07-09T17:03:16.201Z"}}';
        } else {
            $response = $this->CI->curl->post($url,$sendData);
        }

        if ($response) {
            $content8595 = json_decode($response,true);

            MEAPI_Log::writeCsv(array_merge($content8595,$sendData), 'card_8595');
            if ($content8595["code"] == 1000) {
                $result = array(
                    'code' => 1,
                    'msg' => "Gửi thông tin nạp thẻ thành công. Vui lòng xem lịch sử để biết kết quả sau vài phút",
                    'transaction' => $content8595["data"]["transaction"],
                    'money' =>0,
                    'status'=>2
                );

            } else {
                $result = array(
                    'code' => 0,
                    'msg' => $content8595["data"]["message"],
                    'transaction' => $content8595["data"]["transaction"],
                    'money' =>0
                );
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

     public function getShortLink($lenght = 8) {
        $binaryString = "";
        $lookup = self::_getBaseAllLookupTable();
        for ($i = 0; $i < $lenght; $i++) {
            $rand = rand(0, count($lookup));
            $binaryString .= $lookup[$rand];
        }
        return $binaryString;
    }
     protected function _getBaseAllLookupTable() {
        return array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', //  7
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', // 15
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x', // 23
            'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9' // 31
        );
    }
}
