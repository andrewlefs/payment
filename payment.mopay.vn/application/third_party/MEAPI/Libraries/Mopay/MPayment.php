<?php

class MPayment {

    /**
     * @var CI_Controller
     */
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function get_sms_transaction($mobo_id, $sms_transaction_id) {
        $this->CI->load->MEAPI_Model('PaymentModel');
        $result = $this->CI->PaymentModel->get_sms_transaction($mobo_id, $sms_transaction_id);
        if (is_array($result) && empty($result['mobo_id']) === FALSE) {
            $response = array(
                'ip' => $result['ip'],
                'language' => $result['language'],
                'user_agent' => $result['user_agent'],
                'platform' => $result['platform'],
                'mobo_id' => $result['mobo_id'],
                'scope_id' => $result['scope_id'],
                'direct' => $result['direct'],
                'info' => $result['data'],
                'channel' => $result['channel'],
                'mobo_service_id' => $result['mobo_service_id']
            );
            return $response;
        }
        return FALSE;
    }

    public function get_items($service_id) {
        $this->CI->load->MEAPI_Model('InsideModel');
        $params['service_id'] = intval($service_id);
        $result = $this->CI->InsideModel->get_items($params);
        if ($result) {
            foreach ($result as $key => $value) {
                if ($value['visible']) {
                    $response[] = array(
                        'itemId' => $value['item_id'],
                        'itemName' => $value['item_name'],
                        'credit' => $value['credit'],
						'local_only' => $value['local_only']
                    );
                }
            }
            return $response;
        }
        return array();
    }

    public function item_info($item_id) {
        $this->CI->load->MEAPI_Model('InsideModel');
        $params['item_id'] = $item_id;
        $result = $this->CI->InsideModel->select_items($params);
        if ($result) {
            return $result['credit'];
        }
        return FALSE;
    }

}
