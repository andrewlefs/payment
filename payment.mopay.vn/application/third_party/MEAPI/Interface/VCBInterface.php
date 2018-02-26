<?php

interface MEAPI_Interface_VCBInterface extends MEAPI_Response_ResponseInterface {

    public function active(MEAPI_RequestInterface $request);

    public function deactive(MEAPI_RequestInterface $request);

    public function deposit(MEAPI_RequestInterface $request);

    public function withdraw(MEAPI_RequestInterface $request);

}

