<?php

class MEAPI_Response_HTMLResponse extends MEAPI_Response {

    protected $_code = array();

    public function __construct(MEAPI_RequestInterface $request, $html = 'null') {
        $parameters = $html;
        parent::__construct($parameters, $statusCode);
    }

}

?>
