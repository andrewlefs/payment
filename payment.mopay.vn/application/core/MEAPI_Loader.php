<?php

require_once APPPATH . 'third_party/MEAPI/Autoloader.php';

class MEAPI_Loader extends CI_Loader {

    public function __construct() {
        parent::__construct();
    }

    public function MEAPI_Model($model_name) {
        $this->model('../third_party/MEAPI/Models/' . $model_name);
    }

    public function MEAPI_Library($library_name, $alias = NULL) {
        if (empty($alias) == TRUE) {
            $alias = $library_name;
        }
        $this->library('../third_party/MEAPI/Libraries/' . $library_name, FALSE, $alias);
    }

    public function MEAPI_Helper($helper_name, $alias = NULL) {
        if (empty($alias) == TRUE) {
            $alias = $helper_name;
        }
        $this->helper('../third_party/MEAPI/Helpers/' . $helper_name, $alias);
    }

}
