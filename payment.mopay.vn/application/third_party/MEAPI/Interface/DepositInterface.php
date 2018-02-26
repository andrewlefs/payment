<?php

interface MEAPI_Interface_DepositInterface extends MEAPI_Response_ResponseInterface {

    public function sms_receive_7x65(MEAPI_RequestInterface $request);

    public function sms_receive($sms_gateway, MEAPI_RequestInterface $request);

    public function top(MEAPI_RequestInterface $request);

    public function report(MEAPI_RequestInterface $request);

}
