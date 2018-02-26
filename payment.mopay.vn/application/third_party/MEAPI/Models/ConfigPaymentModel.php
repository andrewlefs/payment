<?php

class ConfigPaymentModel extends CI_Model {

    public $_db;
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->load->MEAPI_Library('StoreProcedure');
    }
    public function check_service_id($service_id){
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('app', $service_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('config_app_payment', array('id', 'app'));

        return is_object($data) ? $data->row_array() : FALSE;
    }
    public function getConfigPayment(){
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('config_payment');
        return is_object($data) ? $data->row_array() : FALSE;
    }
    public function getItemGameConfig($app_id,$connection_id){
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('connection_id', $connection_id);
        $this->_db_slave->where('app', $app_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('config_app_payment');
      // echo $this->_db_slave->last_query();
        if (is_object($data)) {
            $data   =   $data->row_array();
            if(!empty($data)){
                return $data;
            }else{
                if (!$this->_db_slave)
                    $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
                $this->_db_slave->where('connection_id', 0);
                $this->_db_slave->where('app', 0);
                $this->_db_slave->limit(1);
                $data = $this->_db_slave->get('config_app_payment');//echo $this->_db_slave->last_query();
                return is_object($data) ? $data->row_array() : FALSE;
            }

        }

        return  FALSE;
    }

    public function getPaymentItemGame($app_id,$connection_id){
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('connection_id', $connection_id);
        $this->_db_slave->where('app', $app_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('config_app_payment');
        if (is_object($data)) {
            $data   =   $data->row_array();
            if(!empty($data)){
                return $data;
            }
            return  FALSE;
        }

        return  FALSE;
    }

    public function saveItemconfig_game($params){
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        }
        $result = StoreProcedure::call('config_payment_game_insert', $params, $this->_db);
        // echo $this->_db->last_query();die;
        if (is_object($result)) {
            $data = $result->row_array();
            if ($data['result'])
                return TRUE;
            return FALSE;
        }else {
            return FALSE;
        }
    }
    public function updateItemconfig_game($params){
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        }

        $result = StoreProcedure::call('config_payment_game_update', $params, $this->_db);
        // echo $this->_db->last_query();die;
        if (is_object($result)) {
            $data = $result->row_array();
            if ($data['result'])
                return TRUE;
            return FALSE;
        }else {
            return FALSE;
        }
    }

    public function config_payment_update($params){
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        }
        $result = StoreProcedure::call('config_payment_update', $params, $this->_db);

        if (is_object($result)) {
            $data = $result->row_array();$this->_db->last_query();
            if ($data['result'])
                return TRUE;
            return FALSE;
        }else {
            return FALSE;
        }
    }
    public function config_payment_all_game($params){
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        }
        $result = StoreProcedure::call('config_payment_all_game', $params, $this->_db);

        if (is_object($result)) {
            $data = $result->row_array();$this->_db->last_query();
            if ($data['result'])
                return TRUE;
            return FALSE;
        }else {
            return FALSE;
        }
    }
    public function config_payment_ME($params){
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        }
        $result = StoreProcedure::call('config_payment_me', $params, $this->_db);

        if (is_object($result)) {
            $data = $result->row_array();$this->_db->last_query();
            if ($data['result'])
                return TRUE;
            return FALSE;
        }else {
            return FALSE;
        }
    }


}

