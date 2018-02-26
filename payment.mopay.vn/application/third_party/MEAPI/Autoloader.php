<?php

class MEAPI_Autoloader {

    private $dir;

    public function __construct($dir = null) {
        if (is_null($dir)) {
            $dir = APPPATH . 'third_party/';
        }
        $this->dir = $dir;
    }

    public static function register($dir = null) {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_extensions(".php");
        spl_autoload_register(array(new self($dir), 'autoload'));
    }

    public function autoload($class) {
        if (0 !== strpos($class, 'MEAPI') && 0 !== strpos($class, 'Store')) {
            return;
        }
        $file = $this->dir . '/' . str_replace('_', '/', $class) . '.php';
        if (file_exists($file) === TRUE)
            include_once $file;
    }

}

?>
