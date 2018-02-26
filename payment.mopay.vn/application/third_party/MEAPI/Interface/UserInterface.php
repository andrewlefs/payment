<?php

interface MEAPI_Interface_UserInterface extends MEAPI_Response_ResponseInterface {

    public function authorize(MEAPI_RequestInterface $request);

    public function authorize_facebook(MEAPI_RequestInterface $request);

    public function register_facebook(MEAPI_RequestInterface $request);

    public function map_account_facebook(MEAPI_RequestInterface $request);

    public function register_quickly(MEAPI_RequestInterface $request);

    public function verify_access_token(MEAPI_RequestInterface $request);

    public function check_phone(MEAPI_RequestInterface $request);

    public function check_mobo_id(MEAPI_RequestInterface $request);

    public function send_code_active(MEAPI_RequestInterface $request);

    public function send_code_forget(MEAPI_RequestInterface $request);

    public function forgot_password(MEAPI_RequestInterface $request);

    public function change_password(MEAPI_RequestInterface $request);

    public function active_account(MEAPI_RequestInterface $request);

    public function get_account_trial(MEAPI_RequestInterface $request);

    public function update_info(MEAPI_RequestInterface $request);

    public function list_service(MEAPI_RequestInterface $request);

    public function add_service(MEAPI_RequestInterface $request);

    public function logout(MEAPI_RequestInterface $request);

    public function ntp(MEAPI_RequestInterface $request);
}
