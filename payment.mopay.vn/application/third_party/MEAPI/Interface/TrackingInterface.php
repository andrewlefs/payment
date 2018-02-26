<?php

interface MEAPI_Interface_TrackingInterface extends MEAPI_Response_ResponseInterface {

    public function deposit_card(MEAPI_RequestInterface $request);

    public function deposit_sms(MEAPI_RequestInterface $request);

    public function deposit_banking(MEAPI_RequestInterface $request);

}

