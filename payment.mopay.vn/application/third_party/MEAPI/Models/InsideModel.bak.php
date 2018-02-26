<?php

class InsideModel extends CI_Model {

    public $_db;
    public $_db_slave;
    private $CI;
    private $_tbl_payment = 'payment';
    private $_tbl_deposit = 'deposits';
    private $_tbl_withdraw = 'withdraws';
    private $_tbl_wallet = 'wallets';
    private $_tbl_wallet_history = 'wallet_historys';
    private $_tbl_withdraw_history = 'withdraw_historys';

    public function __construct() {
        $this->CI = &get_instance();
        $this->load->MEAPI_Library('StoreProcedure');
    }

    public function get_balance($mobo_id) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }
        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $result = $this->_db_slave->get($this->_tbl_wallet);
        return is_object($result) ? $result->row_array() : FALSE;
    }

    public function deposit_history($from, $to, $offset, $limit, $type) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        if ($type != NULL) {
            $this->_db_slave->where('type', $type);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS *', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->limit($limit, $offset);
        $result = $this->_db_slave->get($this->_tbl_deposit);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function wallet_history($from, $to, $offset, $limit) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS *', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->limit($limit, $offset);
        $result = $this->_db_slave->get($this->_tbl_wallet_history);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function withdraw_history($from, $to, $offset, $limit) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS *', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->limit($limit, $offset);
        $result = $this->_db_slave->get($this->_tbl_withdraw_history);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function get_top_deposit($from, $to, $offset, $limit, $type = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        if ($type != NULL) {
            $this->_db_slave->where('type', $type);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS `mobo_id`, SUM(`money`) AS `total_money`', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->group_by('mobo_id');
        $this->_db_slave->order_by('total_money', 'DESC');
        $this->_db_slave->limit($limit, $offset);
        $result = $this->_db_slave->get($this->_tbl_deposit);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function get_top_withdraw($from, $to, $offset, $limit, $type = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        if ($type != NULL) {
            $this->_db_slave->where('type', $type);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS `mobo_id`, SUM(`credit`) AS `total_money`', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->group_by('mobo_id');
        $this->_db_slave->order_by('total_money', 'DESC');
        $this->_db_slave->limit($limit, $offset);
        $result = $this->_db_slave->get($this->_tbl_withdraw_history);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function report_deposit($from, $to, $type = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        if ($type != NULL) {
            $this->_db_slave->where('type', $type);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS SUM(`money`) AS `money`, DATE(`datetime_create`) AS `datetime`', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->group_by('DATE(`datetime_create`)', FALSE);
        $result = $this->_db_slave->get($this->_tbl_deposit);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function report_withdraw($from, $to, $type = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS SUM(`credit`) AS `money`, DATE(`datetime_create`) AS `datetime`', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
		$this->_db_slave->where('status', 1);
        $this->_db_slave->group_by('DATE(`datetime_create`)', FALSE);
        $result = $this->_db_slave->get($this->_tbl_withdraw_history);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function report_wallet($from, $to, $type = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }

        $this->_db_slave->select('SQL_CALC_FOUND_ROWS SUM(`credit`) AS `money`, DATE(`datetime_create`) AS `datetime`', FALSE);
        $this->_db_slave->where('datetime_create >= ', format_from_date($from));
        $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        $this->_db_slave->group_by('DATE(`datetime_create`)', FALSE);
        $result = $this->_db_slave->get($this->_tbl_wallet_history);
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function total_rows() {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }
        $sql = 'SELECT FOUND_ROWS() as `total`';
        $result = $this->_db_slave->query($sql);
        return is_object($result) ? current($result->row_array()) : FALSE;
    }

    public function get_deposit_history($params) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }
        $result = StoreProcedure::call('sp_deposits_get', $params, $this->_db_slave);        
        if (is_object($result))
            return $result->row_array();
    }
    
    public function get_withdraw_history($params) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'payment_info', 'type' => 'slave'), TRUE);
        }
        $result = StoreProcedure::call('sp_withdraw_get', $params, $this->_db_slave);        
        if (is_object($result))
            return $result->row_array();
    }

}
