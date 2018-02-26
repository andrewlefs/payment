<?php

class MEAPI_Core_Bootstrap {
    
    /**
     *
     * @var CI_Controller
     */
    protected $CI;
    protected $_response;

    public function getResponse() {
        return $this->_response;
    }

    function __construct() {
        $this->CI = & get_instance();
    }

}
