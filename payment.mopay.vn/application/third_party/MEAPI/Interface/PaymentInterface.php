<?php

interface MEAPI_Interface_PaymentInterface extends MEAPI_Response_ResponseInterface {

    public function get_sms_content(MEAPI_RequestInterface $request);

    public function verify_card(MEAPI_RequestInterface $request);

    public function verify_sandbox(MEAPI_RequestInterface $request);

    public function verify_card_direct(MEAPI_RequestInterface $request);

    public function verify_inapp_apple(MEAPI_RequestInterface $request);

    public function verify_inapp_google(MEAPI_RequestInterface $request);

    public function get_payment_list(MEAPI_RequestInterface $request);

    public function exchange_credit(MEAPI_RequestInterface $request);

    public function rate_to_service(MEAPI_RequestInterface $request);

    public function withdraw(MEAPI_RequestInterface $request);

    public function verify_withdraw_transaction(MEAPI_RequestInterface $request);

    public function get_link_banking(MEAPI_RequestInterface $request);

    public function get_payment_exchange(MEAPI_RequestInterface $request);

    public function get_banking_price(MEAPI_RequestInterface $request);

    public function receive_banking_ipn(MEAPI_RequestInterface $request);

    public function receive_banking_success(MEAPI_RequestInterface $request);

    public function receive_banking_fail(MEAPI_RequestInterface $request);

    public function balance(MEAPI_RequestInterface $request);

    public function history_deposit(MEAPI_RequestInterface $request);

    public function history_withdraw(MEAPI_RequestInterface $request);

    public function get_app_exchange(MEAPI_RequestInterface $request);
}
