<?php

class MEAPI_Server {

    public $request;
    protected $_response;
    private $CI;

    public function __construct($data = NULL) {
        $this->CI = & get_instance();
        $this->request = MEAPI_Request::createFromGlobals($data);
        $this->CI->load->helper('url');
    }

    public function start() {
        $controller = $this->request->get_controller();
        $function = $this->request->get_function();

        if (method_exists($controller, $function) == TRUE) {
            $method = new $controller();
            $method->{$function}($this->request);
            $this->_response = $method->getResponse();
        } else {
            $this->_response = new MEAPI_Response(array('Welcome to MEAPI !!!'));
        }
    }

    public function getResponse() {
        return $this->_response;
    }

}

?>
