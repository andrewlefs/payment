<?php

interface MEAPI_Interface_AdapterInterface extends MEAPI_Response_ResponseInterface {

    public function get_payment_list(MEAPI_RequestInterface $request);
   

}

