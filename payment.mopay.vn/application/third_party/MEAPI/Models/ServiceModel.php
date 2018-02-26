<?php

class ServiceModel extends CI_Model {

    public $_db;
    public $_db_slave;
    private $CI;
    private $_tbl_payment = 'payment_data_detail';
    private $_tbl_config = 'msv_configs';


    public function __construct() {
        $this->CI = &get_instance();
//        $this->load->MEAPI_Library('StoreProcedure');
    }

    public function get_msv_config($where = array())
    {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'service_info', 'type' => 'slave'), TRUE);
        }
        if (empty($where) == FALSE)
            $this->_db_slave->where($where);
        $this->_db_slave->limit(1);
        $result = $this->_db_slave->get($this->_tbl_config);
        return $result == TRUE ? $result->row_array() : FALSE;
    }


    public function get_info($where = '', $fields = "*", $order_by = 'id', $desc = 'desc') {

        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'service_info', 'type' => 'slave'), TRUE);
        }

        if (is_array($where)) {
            $this->_db_slave->select($fields);
            $this->_db_slave->where($where);
            $this->_db_slave->order_by($order_by, $desc);
            $result = $this->_db_slave->get($this->_tbl_payment);
        } else {
            $this->_db_slave->select($fields);
            $this->_db_slave->order_by($order_by, $desc);
            $result = $this->_db_slave->get($this->_tbl_payment);
        }
        return is_object($result) == TRUE ? $result->row_array() : FALSE;
    }

}
