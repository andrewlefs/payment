<?php

class MEAPI_Controller_BlackboxController extends MEAPI_Core_Bootstrap implements MEAPI_Interface_BlackboxInterface {

    public function gen(MEAPI_RequestInterface $request) {
        $authorize = new MEAPI_Controller_AuthorizeController();
        if ($authorize->validateAuthorizeRequest($request, array('control' => __CLASS__, 'func' => __FUNCTION__)) == TRUE) {
            $params = $request->input_request();
            $needle = array('token_login', 'mobo_id', 'mobo_service_id', 'access_token', 'device_id', 'user_agent', 'channel', 'platform', 'telco', 'version');
            if (is_required($params, $needle) == TRUE) {
                
            } else {
                $this->_response = new MEAPI_Response_APIResponse($request, 'INVALID_PARAMS');
            }
        } else {
            $this->_response = $authorize->getResponse();
        }
    }

}
