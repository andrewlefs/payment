<?php

class WithdrawModel extends CI_Model {

    public $_db;
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->load->MEAPI_Library('StoreProcedure');
        $this->CI->load->helper('utils');
    }

    public function insert_withdraw_history($data) {
        /*
            $data = array(
                'mobo_id' => $data,
                'credit' => $data,
                'withdraw_transaction' => $data,
                'ip_called' => $data,
                'ip_user' => $data,
                'language' => $data,
                'user_agent' => $data,
                'platform' => $data
            );
         */
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_withdraw_historys_insert', array(
            $data['mobo_id'], $data['credit'], NULL,
            $data['withdraw_transaction'], 0, $data['ip_called'],
            $data['ip_user'], NULL, $data['language'],
            $data['user_agent'], $data['platform']), $this->_db);

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

    public function finish_withdraw($data) {
        /*
            $data = array(
                'mobo_id' => $data,
                'blackbox_transaction' => $data,
                'withdraw_transaction' => $data,
                'credit' => $data,
                'result_item' => $data,
                'channel' => $data,
                'provider' => $data,
                'scope_id' => $data,
                'service_id' => $data
            );
         */

        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_withdraw_insert', array(
            $data['mobo_id'], $data['blackbox_transaction'], $data['withdraw_transaction'],
            NULL, $data['credit'], 0, NULL,
            $data['channel'], $data['provider'], $data['scope_id'], NULL
        ), $this->_db);

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

    public function verify_transaction($transaction_id, $result_items, $service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        $this->_db->where('withdraw_transaction', $transaction_id);
        $this->_db->where('verify', 0);
        $this->_db->update('withdraws', array('result_items' => $result_items, 'service_id' => $service_id, 'verify' => 1));
        $result = $this->_db->affected_rows();
        if ($result > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function history($mobo_id, $offset = 0, $limit = 10, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SQL_CALC_FOUND_ROWS *','credit', 'result_items AS description', 'datetime_create', 'name AS service_name'),FALSE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', format_from_date($from));
            $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        }
        $this->_db_slave->order_by("datetime_create", "desc");        
        $this->_db_slave->join('app', 'app.id = withdraws.scope_id');        
        $result = $this->_db_slave->get('withdraws', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        $query = $this->_db_slave->query('SELECT FOUND_ROWS() AS `Count`');
        $total_rows = $query->row()->Count;
        if (empty($result) === FALSE) {            
            return array('code' => 1, 'detail' => array('rows' => $result, 'total_rows' => $total_rows));            
        } else {
            return array(
                'code' => 0
            );
        }
    }

    public function top($filter = NULL, $offset = 0, $limit = 10, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        }
        if ($limit > 100)
            $limit = 100;
        $this->_db_slave->select(array('SUM(credit) AS credit', 'mobo_id'));
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', format_from_date($from));
            $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        }

        if(is_array($filter) === TRUE){
            foreach ($filter as $kf => $vf) {
                $this->_db_slave->where($kf, $vf);
            }
        }

        $this->_db_slave->group_by('mobo_id');
        $this->_db_slave->order_by('credit', 'desc');
        $result = $this->_db_slave->get('withdraws', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        if (empty($result) === FALSE) {
            return array(
                'code' => 1,
                'detail' => $result
            );
        } else {
            return array(
                'code' => 0
            );
        }
    }

    public function report($filter = NULL, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(credit) AS credit'));
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', format_from_date($from));
            $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        }		
        if(is_array($filter) === TRUE){
            foreach ($filter as $kf => $vf) {
				if($kf != 'scope_id' && $kf != 'service_id'){
					$this->_db_slave->where($kf, $vf);	
				}                
            }
        }		
		$this->_db_slave->where('(`service_id` = '.$filter['service_id'].' OR `scope_id` =  '.$filter['scope_id'].')');	
				
        $this->_db_slave->order_by('credit', 'desc');		
        $result = $this->_db_slave->get('withdraws');
		
        $result = is_object($result) ? $result->row_array() : FALSE;
        if (empty($result) === FALSE) {
            return array(
                'code' => 1,
                'detail' => $result
            );
        } else {
            return array(
                'code' => 0
            );
        }
    }
    
    public function report_detail($from, $to) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'withdraw_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(credit) AS credit',  'scope_id AS service_id','service_id AS partner_id'));
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', format_from_date($from));
            $this->_db_slave->where('datetime_create <= ', format_to_date($to));
        }        
        $this->_db_slave->group_by(array('scope_id','service_id'));
        $this->_db_slave->order_by('credit', 'desc');
        $result = $this->_db_slave->get('withdraws');
        //echo $this->_db_slave->last_query();
        $result = is_object($result) ? $result->result_array() : FALSE;
        if (empty($result) === FALSE) {
            return array(
                'code' => 1,
                'detail' => $result
            );
        } else {
            return array(
                'code' => 0
            );
        }
    }
}
