<?php

class MEAPI_Request implements MEAPI_RequestInterface {

    protected $_controller;
    protected $_function;
    protected $_app;
    protected $_lang;
    private $_get;
    private $_post;
    private $_request;
    protected $controller_map = array();
    protected $function_map = array();

    public function __construct($get = array(), $post = array()) {
        $this->_get = $get;
        $this->_post = $post;
        $this->_request = array_merge($get, $post);
        if ($this->_request['q']) {
            $get_params = decrypt_url($this->_request['q']);
            parse_str($get_params, $get_params);
            if (empty($this->_request['control']) === FALSE) {
                unset($get_params['control'], $get_params['func']);
            }
            $this->_request = array_merge($this->_request, $get_params);
            unset($this->_request['q']);
        }
        $this->controller_map = MEAPI_Config_Map::getController();
        $this->function_map = MEAPI_Config_Map::getFunction();
    }

    public static function createFromGlobals($data = NULL) {
        $CI = &get_instance();
        $class = __CLASS__;
        if (empty($data) === TRUE) {
            $tmp_get = $CI->input->get(NULL, TRUE);
            $tmp_post = $CI->input->post(NULL, TRUE);
        } else {
            $tmp_get = $data;
        }
        if (is_array($tmp_get) == FALSE)
            $tmp_get = array();
        if (is_array($tmp_post) == FALSE)
            $tmp_post = array();
        $get = array_change_key_case($tmp_get, CASE_LOWER);
        $post = array_change_key_case($tmp_post, CASE_LOWER);
        unset($tmp_get);
        unset($tmp_post);
        $request = new $class($get, $post);
        return $request;
    }

    public function get_controller() {
        if ($this->_controller)
            return $this->_controller;

        $controller = $this->input_request('control');
        if (isset($controller)) {
            $this->_controller = strtolower($controller);
            if ($this->controller_map[$this->_controller])
                $this->_controller = $this->controller_map[$this->_controller];
            return $this->_controller;
        } else {
            return NULL;
        }
    }

    public function get_function() {
        if ($this->_function)
            return $this->_function;

        $function = $this->input_request('func');
        if (isset($function)) {
            $this->_function = strtolower($function);
            if ($this->function_map[$this->_function])
                $this->_function = $this->function_map[$this->_function];
            return $this->_function;
        } else {
            return NULL;
        }
    }

    public function get_app() {
        if ($this->_app)
            return $this->_app;

        $app = $this->input_request('app');
        if (!empty($app)) {
            $this->_app = strtolower($app);
            return $this->_app;
        } else {
            return NULL;
        }
    }

    public function get_lang() {
        if ($this->_lang)
            return $this->_lang;

        $lang = $this->input_request('lang');
        if (!empty($lang)) {
            $this->_lang = strtolower($lang);
            return $this->_lang;
        } else {
            return 'default';
        }
    }

    public function input_get($name = NULL, $default = FALSE) {
        if ($name == NULL) {
            return $this->_get;
        } else {
            $name = strtolower($name);
            $data = $this->_get[$name];
            if (isset($data) == TRUE) {
                return $data;
            } else {
                return $default;
            }
        }
    }

    public function input_post($name = NULL, $default = FALSE) {
        if ($name == NULL) {
            return $this->_post;
        } else {
            $name = strtolower($name);
            $data = $this->_post[$name];
            if (isset($data) == TRUE) {
                return $data;
            } else {
                return $default;
            }
        }
    }

    public function input_request($name = NULL, $default = FALSE, $is_authorize = FALSE) {
        if ($name == NULL) {
            if (empty($this->_request['card']) === FALSE && $is_authorize === FALSE) {
                $this->_request['telco'] = $this->_request['card'];
            }
			
			if(empty($this->_request['lang']) === FALSE){
				$this->_request['language'] = $this->_request['lang'];
			}
            if(empty($this->_request['ip_user']) === FALSE){
                $this->_request['ip'] = $this->_request['ip_user'];
            }
            /*
             * Hỗ trợ mopay cho windows phone, flow nạp thẳng, 06/11/2015
             */
            if($this->_request['platform'] == 'wp' && ($this->_request['app'] == 'bog' || $this->_request['app'] == 'mgh' || $this->_request['app'] == 'phongthan' || $this->_request['app'] == '139') && $is_authorize === FALSE){
                $this->_request['direct'] = 1;
                if(empty($this->_request['ip']) === TRUE){
                    $this->_request['ip'] = $_SERVER['REMOTE_ADDR'];
                }
            }
            return $this->_request;
        } else {
            $name = strtolower($name);
            $data = $this->_request[$name];
            if (isset($data) == TRUE) {
                return $data;
            } else {
                return $default;
            }
        }
    }

}

?>
