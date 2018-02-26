<?php

class MecorpPayment{

    private $config;

    public function __construct(){
        $this->CI = &get_instance();
        $this->config = MEAPI_Config_Mecorp::payment();
        $this->CI->load->MEAPI_Library('Curl');
    }

    public function get_app_exchange($service_id,$credit=1){
        $params = array(
            'credit' => $credit,
            'service_id' => $service_id,
            'app' => $this->config['app']
        );
        $params['token'] = md5(implode('',$params).$this->config['secret']);
        $url = $this->config['url'].'/mopay/exchange?'.http_build_query($params);
		//echo $url;
        $response = json_decode( $this->CI->Curl->get($url),TRUE);
        if(is_array($response) && $response['code'] == 1){
            return array(
                'unit' => $response['data']['unit'],
                'credit' => $response['data']['money'],
                'rate' => $response['data']['rate']
            );
        }else{
            return false;
        }
    }
}