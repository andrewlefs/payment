<?php

interface MEAPI_Interface_WithdrawInterface extends MEAPI_Response_ResponseInterface {

    public function top(MEAPI_RequestInterface $request);

    public function history(MEAPI_RequestInterface $request);

    public function report(MEAPI_RequestInterface $request);

}
