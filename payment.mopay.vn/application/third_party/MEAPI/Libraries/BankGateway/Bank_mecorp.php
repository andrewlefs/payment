<?php

/**
 * Description of CardGate
 *
 * @author thainpv
 */
class Bank_mecorp {

    private $CI;
    private $_params = array();
    private $env = 'real';
    private $_bank_connection_id = 1;

    private $config = array(
        'real' => array(
            'partner_id' => 1025,
            'private_key' => '5361f20d-34d5-11e5-992e-5cf3fc4a',
            'success_url' => 'http://payment.mopay.vn/?control=payment&func=receive_banking_success&connection_name=mecorp',
            'fail_url' => 'http://payment.mopay.vn/?control=payment&func=receive_banking_fail&connection_name=mecorp',
			//'fail_url' => 'https://mopay.vn/nap-mcoin.html',
            'gateway' => 'http://123.30.133.183:9000/BankingGateway2/QueryTransactionStatus',
            'url' => 'http://bank.mopay.vn/api',
            'url_credit_card' => 'http://bank.mopay.vn/external',
        ),
        'sandbox' => array(
            'partner_id' => 1009,
            'private_key' => 'dbed52b21f325a13800d921f90899f3f',
            'success_url' => 'http://payment.mopay.dev/?control=payment&func=receive_banking_success&connection_name=mecorp',
            'fail_url' => 'http://payment.mopay.dev/?control=payment&func=receive_banking_fail&connection_name=mecorp',
            'gateway' => 'http://113.161.78.101:8080/BankingGateway/QueryTransactionStatus',
            'url' => 'http://sandbox.bank.mopay.vn/api',
            'url_credit_card' => 'http://bank.mopay.vn/external',
        )
    );

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->MEAPI_library('curl');
        $this->config = $this->config[$this->env];
    }

    public function get_connection_id() {
        return $this->_bank_connection_id;
    }

    public function set_params($params) {
        /*
            $params = array(
                'banking_transaction' => $data,
                'mobo_id' => $data,
                'money' => $data,
                'bank_type' => $data,
                'data' => $data,
                'bank_code' => $data
            );
         */
        $this->_params = $params;
    }

    public function get_link($credit_card = FALSE) {
        $data = array(
            'partner_id' => $this->config['partner_id'],
            'partner_trans_id' => $this->_params['banking_transaction'],
            'username' => $this->_params['mobo_id'],
            'amount' => $this->_params['money'],
            'bank_type' => $this->_params['bank_type'],
            'fail_url' => $this->config['fail_url'],
            'success_url' => $this->config['success_url'],
            'data' => $this->_params['data'],
            'service_name' => APP_NAME,
            'bank_code' => $this->_params['bank_code'],
        );
        $base64 = base64_encode(json_encode($data));
        $token = $this->make_token($base64);
        $args = array(
            'data' => $base64,
            'token' => $token,
        );

        if($credit_card === TRUE){
            $url = $this->config['url_credit_card'];
        }else{
            $url = $this->config['url'];
        }

        return array(
            'code' => 1,
            'url' => $url . '?' . http_build_query($args)
        );
    }

    public function validate_data($status, $data, $token) {
        $arrCheck = array(
            'status' => $status,
            'data' => $data
        );
        if ($this->make_token(implode('', $arrCheck)) == $token) {
            return TRUE;
        }
        return FALSE;
    }

    public function validate_ipn($params, $token) {
        if ($this->make_token(implode('', $params)) == $token) {
            return TRUE;
        }
        return FALSE;
    }

    public function verify_transaction($transaction_id) {
        $token = $this->make_token($this->config['partner_id'] . $transaction_id);
        $link = $this->config['gateway'] . '?' . 'partner_id=' . $this->config['partner_id'] . '&partner_trans_id=' . $transaction_id . '&token=' . $token;
        $output = $this->CI->curl->get($link);
        if (empty($output) === FALSE) {
            $json = json_decode($output, TRUE);
            if (empty($json[0]) === FALSE) {
                return $json[0];
            }
        }
        return FALSE;
    }

    private function make_token($params) {
        return md5($params . $this->config['private_key']);
    }


}
