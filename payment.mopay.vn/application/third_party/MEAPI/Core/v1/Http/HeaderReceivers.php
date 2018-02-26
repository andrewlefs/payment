<?php



namespace Misc\Http;

class HeaderReceivers extends \ArrayObject {

    public function __construct() {
        $params = array();
        foreach ($_SERVER as $key => $value) {            
            if (strpos(strtolower($key), "http_") === 0) {
                $params[str_replace("http_", "", strtolower($key))] = $value;
            }
        }
        parent::__construct($params);
    }

}
