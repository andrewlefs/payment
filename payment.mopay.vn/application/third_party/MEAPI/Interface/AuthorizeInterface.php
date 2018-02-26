<?php

interface MEAPI_Interface_AuthorizeInterface extends MEAPI_Response_ResponseInterface {

    public function validateAuthorizeRequest(MEAPI_RequestInterface $request, $scope = array());    
}