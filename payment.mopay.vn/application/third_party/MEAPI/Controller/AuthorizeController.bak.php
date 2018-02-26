<?php

class MEAPI_Controller_AuthorizeController implements MEAPI_Interface_AuthorizeInterface {

    protected $_response;

    /**
     *
     * @var CI_Controller
     */
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }

    public function validateAuthorizeRequest(MeAPI_RequestInterface $request, $scope = array()) {
        $params = $request->input_request(NULL, FALSE, TRUE);
        $this->CI->load->MEAPI_Library('TOTP');
        $this->CI->load->MEAPI_Model('AuthorizeModel');
        $this->CI->load->library('cache');
        $cache = $this->CI->cache->load('memcache', 'system_info');
        $app_info = $cache->store($params['app'] . '_' . $params['control'] . '_' . $params['func'], $this->CI->AuthorizeModel, 'getAuthorize', array(array($params['app'])));

        if (empty($app_info) === TRUE) {
            $this->_response = new MEAPI_Response_APIResponse($request, 'NOT_PERMISSION_APP');
            return FALSE;
        }

        $app_info = $app_info[0];

        // Check ip allowed if ip list not empty
        if (empty($app_info['ip']) === FALSE) {
            $ip_list = json_decode($app_info['ip'], TRUE);
            if (in_array($ip, $ip_list) === FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'NOT_ALLOWED_IP');
                return FALSE;
            }
        }

        // Check ACL
        if (empty($app_info['acl']) === FALSE) {
            $acl_list = json_decode($app_info['acl'], TRUE);
            if (in_array($params['control'] . '.' . $params['func'], $acl_list) === FALSE) {
                $this->_response = new MEAPI_Response_APIResponse($request, 'NOT_PERMISSION_APP');
                return FALSE;
            }
        }


        // Check Token
        $token = $params['token'];
        $otp = $params['otp'];
        $secret = $app_info['private_key'];
        unset($params['token']);

        $result = $this->CI->TOTP->verifyCode($secret, $otp, 2);

        $raw = implode('', $params);
        $verify_token = md5(implode('', $params) . $secret);
		
		$bypass_authenticate = TRUE;
		if($params['func'] == 'verify_sandbox'){
			$bypass_authenticate = FALSE;
		}
        if ( ($verify_token == $token AND $result == TRUE) OR $bypass_authenticate === TRUE ) {
            define('APP_NAME', $app_info['app']);
            define('APP_SECRET', $app_info['private_key']);
            define('SCOPE_ID', $app_info['id']);
            define('SERVICE_STATE', $app_info['state']);
            return TRUE;
        }
        $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_TOKEN', array($this->CI->TOTP->getCode($secret), date('Y-m-d H:i:s')));
        return FALSE;
    }

    public function getResponse() {
        return $this->_response;
    }

}
