<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class V2 extends CI_Controller {

    public function index() {
		define('API_VERSION', 1);
        $this->benchmark->mark('api_start');
        parent::__construct();

        MEAPI_Autoloader::register();
        $api = new MEAPI_Server();
        $api->start();

        if (is_object($api->getResponse())) {
            $output = $api->getResponse()->getJson();
            if (empty($output) === TRUE) {
                $output = 'HTML';
                $api->getResponse()->send('html');
            } else {
                $api->getResponse()->send();
            }
        } else {
            $response = new MEAPI_Response(array('Welcome to Service ( System Error ) !!!'));
            $output = $response->getJson();
            $response->send();
        }
        $query = '?' . http_build_query($api->request->input_request());
        $this->benchmark->mark('api_end');
        $time_execute = $this->benchmark->elapsed_time('api_start', 'api_end');
        MEAPI_Log::writeCsv(array($time_execute, $query, $output), 'request_' . date('H'));
        exit;
    }

    public function receive($get = NULL) {
        if (empty($get) === FALSE) {
            $get = ltrim($get, '?');
            parse_str($get, $get);
            if (is_array($get))
                $_GET = array_merge($_GET, $get);
        }
        $this->index();
    }

}
