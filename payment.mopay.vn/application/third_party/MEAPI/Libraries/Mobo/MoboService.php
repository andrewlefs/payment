<?php

class MoboService {

    /**
     * @var CI_Controller
     */
    private $CI;
    private $url = 'http://service.mobo.vn/';
    private $secret_key = 'ZIFNEPFG45F3UXB6';
	private $mobo_key = 'U672T54SWKFLDU2W';
    private $app = 'mopay';

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function pay_to_service($params, $sandbox = 0) {
        $args = array(
            'mobo_id' => $params['mobo_id'],
            'money' => $params['money'],
            'credit' => $params['credit'],
            'payment_type' => $params['payment_type'],
            'info' => $params['info'],
            'service_id' => $params['service_id'],
            'channel' => $params['channel'],
            'version' => '1.0',
            'mobo_service_id' => $params['mobo_service_id'],
            'platform' => $params['platform'],
            'language' => $params['language'],
            'tracking_info' => $params['tracking_info'],
            'device_id' => $params['device_id'],
            'receipt_data' => $params['receipt_data'],
            'sandbox' => $sandbox,
			'subtype' => $params['subtype'],
        );
        $result = $this->_call_api('paymentmopay', 'add_money', $args);
        if (is_array($result) == TRUE) {
            if ($result['code'] == 200091) {
                return array(
                    'credit' => $result['data']['money'],
                    'message' => $result['data']['msg'],
                    'money' => $result['data']['vnd'],
                    'unit' => $result['data']['unit'],
                    'mcoin' => $result['data']['credit'],
					'service_data' => $result['data']['service_data']
                );
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
	
	public function _call_bank_postback($url, $params) {       				        
		$signature = $this->make_signature($params, $this->mobo_key);
        $request_uri = http_build_query($params).'&token='.$signature;
        $data = $this->CI->Curl->post($url, $request_uri);   
		//echo '<pre>';
		//print_r($params);
		//print_r($data);
		//die;
		$data = json_decode($data, TRUE);		
        return $data;        
    }
	
    private function make_token($control, $func, $params) {
        $token = md5($control . $func . implode('', $params) . $this->secret_key);
        return $token;
    }
	
	private function make_signature($params, $key) {
        $token = md5(implode('', $params) . $key);
        return $token;
    }

}
