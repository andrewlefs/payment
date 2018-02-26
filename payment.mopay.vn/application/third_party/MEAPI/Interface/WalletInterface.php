<?php

interface MEAPI_Interface_WalletInterface extends MEAPI_Response_ResponseInterface {

    public function deposit(MEAPI_RequestInterface $request);

    public function withdraw(MEAPI_RequestInterface $request);

    public function balance(MEAPI_RequestInterface $request);

    public function top(MEAPI_RequestInterface $request);

    public function report(MEAPI_RequestInterface $request);
}
