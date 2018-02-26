<?php

interface MEAPI_RequestInterface {

    public function get_controller();

    public function get_function();

    public function get_app();

    public function get_lang();

    public function input_get($name = NULL, $default = FALSE);

    public function input_post($name = NULL, $default = FALSE);

    public function input_request($name = NULL, $default = FALSE);
}

?>
