<?php

class PaymentModel extends CI_Model {

    public $_db;
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->load->MEAPI_Library('StoreProcedure');
    }

    public function insert_sms_transaction($data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);

        $this->_db->insert('sms_transactions', $data);
        $result = $this->_db->insert_id();
        if ($result > 0) {
            return array(
                'code' => 1
            );
        } else {
            return array(
                'code' => 0
            );
        }

        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array(
                    'code' => 1
                );
            }
        }
        return array(
            'code' => 0,
            'detail' => array(
                'error_code' => $result['Errcode'],
                'error_string' => $result['Description']
            )
        );
    }

    public function get_sms_transaction($mobo_id, $sms_transaction_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('sms_transaction_id', $sms_transaction_id);
        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('sms_transactions');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_app_exchange() {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('app', 'id as service_id', 'name'));
        $this->_db_slave->where('exchange', 1);
        $data = $this->_db_slave->get('app');
        return is_object($data) ? $data->result_array() : FALSE;
    }

    public function active_vcb($data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);

        $result = $this->_db->insert('vcb_active', $data);
        //echo $this->_db->last_query();
        return empty($result) == FALSE ? $this->_db->insert_id() : FALSE;
    }

    public function deactive_vcb($vcb_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('vcb_id', $vcb_id);
        $this->_db->delete('vcb_active');
        return $this->_db->affected_rows();
    }

    public function check_active_vcb($mobo_id, $vcb_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('vcb_id', $vcb_id);
        $this->_db_slave->or_where('mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('vcb_active');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function check_vcb_service($mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('vcb_active');
        return is_object($data) ? $data->row_array() : FALSE;
    }


}
