<?php

class MEAPI_Response {

    protected $_parameters;
    private $_dataJson;
    private $_dataHTML;
    private $_isJson = FALSE;

    public function __construct($parameters = array(), $statusCode = 200, $headers = array()) {
        if (is_array($parameters)) {
            $this->_isJson = TRUE;
        }
        $this->_parameters = $parameters;
    }

    public function getJson() {
        if ($this->_isJson === FALSE)
            return FALSE;
        if ($this->_dataJson)
            return $this->_dataJson;
        $this->_dataJson = json_encode($this->_parameters);
        return $this->_dataJson;
    }

    public function getArray() {
        if ($this->_dataArray)
            return $this->_dataArray;
        $this->_dataArray = $this->_parameters;
        return $this->_dataArray;
    }

    public function getHTML() {
        if ($this->_dataHTML === FALSE)
            return FALSE;
        if ($this->_dataHTML)
            return $this->_dataHTML;
        $this->_dataHTML = $this->_parameters;
        return $this->_dataHTML;
    }

    public function send($format = 'json') {          
		switch ($format) {
            case 'json':                    
				@header('Access-Control-Allow-Origin: *');  			
                @header('Content-type: application/json');
                if ($this->_dataJson) {
                    echo $this->_dataJson;
                    break;
                }
                echo $this->getJson();
                break;
            case 'html':				
                if ($this->_dataHTML) {
                    echo $this->_dataHTML;
                    break;
                }
                echo $this->getHTML();
                break;
        }
    }

}

?>
