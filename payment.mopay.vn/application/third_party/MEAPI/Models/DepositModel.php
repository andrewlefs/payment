<?php

class DepositModel extends CI_Model {

    public $_db;
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->load->MEAPI_Library('StoreProcedure');
    }

    public function insert_deposit_sms($data) {
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_deposit_sms_insert', array($data['mobo_id'], $data['sms_connection_id'], $data['mo_id'], $data['money'], $data['mo'], NULL, $data['telco'], $data['phone'], $data['service_number'], $data['code'], 0, $data['received_time'], $data['sms_transaction_id'], $data['ip_called'], $data['ip_user'], NULL, NULL, $data['language'], $data['user_agent'], $data['platform'], $data['deposit_transaction'], 0), $this->_db);

        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array('code' => 1);
            }
        }
        return array('code' => 0, 'detail' => array('error_code' => $result['Errcode'], 'error_string' => $result['Description']));
    }

    public function insert_deposit_card($data) {
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_deposit_card_insert', array($data['mobo_id'], $data['money'], date('Y-m-d H:i:s',time()), $data['ip_called'], $data['ip_user'], date('Y-m-d H:i:s',time()), NULL, $data['language'], $data['user_agent'], $data['platform'], $data['card_connection_id'], $data['telco'], $data['serial'], $data['pin'], $data['card_id'], 0, $data['deposit_transaction'],$data['service_id'],$data['channel'],$data['access_token'],$data['game_info']), $this->_db);

        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array('code' => 1);
            }
        }
        return array('code' => 0, 'detail' => array('error_code' => $result['Errcode'], 'error_string' => $result['Description']));
    }

    public function insert_deposit_banking($data) {
        /*
          $data = array(
          'mobo_id',
          'money',
          'ip_called',
          'ip_user',
          'language',
          'user_agent',
          'platform',
          'bank_connection_id',
          'bank_type',
          'bank_code',
          'bank_transaction',
          'deposit_transaction',
          );
         */
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_deposit_bank_insert', array($data['mobo_id'], $data['money'], NULL, $data['ip_called'], $data['ip_user'], NULL, NULL, $data['language'], $data['user_agent'], $data['platform'], $data['bank_connection_id'], $data['bank_type'], $data['bank_code'], $data['bank_transaction'], 1, $data['deposit_transaction']), $this->_db);

        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array('code' => 1);
            }
        }
        return array('code' => 0, 'detail' => array('error_code' => $result['Errcode'], 'error_string' => $result['Description']));
    }

    public function check_mo_id($sms_connection_id, $mo_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('sms_connection_id', $sms_connection_id);
        $this->_db->where('mo_id', $mo_id);
        $this->_db->limit(1);
        $data = $this->_db->get('deposit_sms');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function check_card_deposit_transaction($transaction_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('transaction_id', $transaction_id);
        $this->_db->limit(1);
        $data = $this->_db->get('deposit_card');
        return is_object($data) ? $data->row_array() : FALSE;
    }
	
	public function check_deposit_bank_transaction($bank_transaction) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('bank_transaction', $bank_transaction);
        $this->_db->limit(1);
        $data = $this->_db->get('deposit_banking');
        return is_object($data) ? $data->row_array() : FALSE;
    }
	
	public function check_deposit_transaction($deposit_transaction) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('deposit_transaction', $deposit_transaction);
        $this->_db->limit(1);
        $data = $this->_db->get('deposits');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function update_mt_by_mo_id($sms_connection_id, $mo_id, $mt, $cdr = 0) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('sms_connection_id', $sms_connection_id);
        $this->_db->where('mo_id', $mo_id);
        $this->_db->where('mt', NULL);
        $this->_db->update('deposit_sms', array('mt' => $mt, 'cdr' => $cdr));
        $result = $this->_db->affected_rows();
        if ($result > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function update_card_money($deposit_transaction, $money) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('deposit_transaction', $deposit_transaction);
        $this->_db->where('money', 0);
        $this->_db->update('deposit_card', array('money' => $money));
        $result = $this->_db->affected_rows();
        if ($result > 0) {
            return TRUE;
        }
        return FALSE;
    }
    public function update_card_8595($deposit_transaction, $params) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('transaction_id', $deposit_transaction);
        $this->_db->where('money', 0);
        $this->_db->where('status_code', 2);
        $this->_db->update('deposit_card', $params);
        $result = $this->_db->affected_rows();
        if ($result > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function update_card_status($deposit_transaction, $params) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'payment_info', 'type' => 'master'), TRUE);
        $this->_db->where('deposit_transaction', $deposit_transaction);
        $this->_db->where('money', 0);
        $this->_db->update('deposit_card', $params);
        $result = $this->_db->affected_rows();
        if ($result > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function finish_deposit($data) {
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        /*
          $data = array(
          'mobo_id' => 'data',
          'money' => 'data',
          'type' => 'data',
          'blackbox_transaction' => 'data',
          'deposit_transaction' => 'data',
          'channel' => 'data',
          'provider' => 'data',
          'scope_id' => 'data',
          );
         */
        $store = new Store_Payment_SpDepositInsert();
        $store->p_mobo_id = $data['mobo_id'];
        $store->p_money = $data['money'];
        $store->p_credit = $data['credit'];
        $store->p_TYPE = $data['type'];
        $store->p_blackbox_transaction = $data['blackbox_transaction'];
        $store->p_deposit_transaction = $data['deposit_transaction'];
        $store->p_channel = $data['channel'];
        $store->p_provider = $data['provider'];
        $store->p_scope_id = $data['scope_id'];

        $objResult = $store->execute($this->_db);

        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array('code' => 1);
            }
        }
        return array('code' => 0, 'detail' => array('error_code' => $result['Errcode'], 'error_string' => $result['Description']));
    }

    public function history($mobo_id, $offset = 0, $limit = 10, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }        
        $this->_db_slave->select(array('SQL_CALC_FOUND_ROWS *','money', 'credit', 'type', 'datetime_create', 'scope_id'),FALSE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', $from . ' 00:00:00');
            $this->_db_slave->where('datetime_create <= ', $to . ' 23:59:59');
        }
        $this->_db_slave->order_by("datetime_create", "desc");        
        $result = $this->_db_slave->get('deposits', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        $query = $this->_db_slave->query('SELECT FOUND_ROWS() AS `Count`');
        $total_rows = $query->row()->Count;
        if (empty($result) === FALSE) {            
            return array('code' => 1, 'detail' => array('rows' => $result, 'total_rows' => $total_rows));
        } else {
            return array('code' => 0);
        }
    }

    public function top($filter = NULL, $offset = 0, $limit = 10, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        if ($limit > 100)
            $limit = 100;
        $this->_db_slave->select(array('SUM(money) AS money', 'mobo_id'));
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', $from . ' 00:00:00');
            $this->_db_slave->where('datetime_create <= ', $to . ' 23:59:59');
        }
        if (is_array($filter) === TRUE) {
            foreach ($filter as $kf => $vf) {
                $this->_db_slave->where($kf, $vf);
            }
        }
        $this->_db_slave->group_by('mobo_id');
        $this->_db_slave->order_by('money', 'desc');
        $result = $this->_db_slave->get('deposits', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => $result);
        } else {
            return array('code' => 0);
        }
    }

    public function report($filter = NULL, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(money) AS money'));
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', $from . ' 00:00:00');
            $this->_db_slave->where('datetime_create <= ', $to . ' 23:59:59');
        }
        if (is_array($filter) === TRUE) {
            foreach ($filter as $kf => $vf) {
                $this->_db_slave->where($kf, $vf);
            }
        }
        $result = $this->_db_slave->get('deposits');
        $result = is_object($result) ? $result->row_array() : FALSE;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => $result);
        } else {
            return array('code' => 0);
        }
    }

    public function tracking_card($filter, $offset = 0, $limit = 10) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        if ($limit > 100)
            $limit = 100;
        $this->_db_slave->select(array('SQL_CALC_FOUND_ROWS deposit_card.*,deposits.channel,deposits.scope_id'),FALSE);

        if (is_array($filter) === TRUE) {            
            foreach ($filter as $kf => $vf) {                
                $this->_db_slave->where("deposit_card.$kf",$vf);                                
            }
        }     
        $this->_db_slave->join('deposits','deposits.deposit_transaction = deposit_card.deposit_transaction','left');
        $this->_db_slave->order_by('deposit_card.id', 'desc');        
        $result = $this->_db_slave->get('deposit_card', $limit, $offset);        
        $result = is_object($result) ? $result->result_array() : FALSE;     
        //echo $this->_db_slave->last_query();
        $query = $this->_db_slave->query('SELECT FOUND_ROWS() AS `Count`');
        $total_rows = $query->row()->Count;
        
        if (empty($result) === FALSE) {            
            return array('code' => 1, 'detail' => array('rows' => $result, 'total_rows' => $total_rows));
        }
        return array('code' => 0);
    }

    public function tracking_sms($filter, $offset = 0, $limit = 10) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        if ($limit > 100)
            $limit = 100;
        $this->_db_slave->select(array('SQL_CALC_FOUND_ROWS deposit_sms.*,deposits.channel,deposits.scope_id'),FALSE);

        if (is_array($filter) === TRUE) {
            foreach ($filter as $kf => $vf) {
                $this->_db_slave->where("deposit_sms.$kf", $vf);
            }
        }       
        $this->_db_slave->join('deposits','deposits.deposit_transaction = deposit_sms.deposit_transaction','left');
        $this->_db_slave->order_by('deposit_sms.id', 'desc');                
        $result = $this->_db_slave->get('deposit_sms', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        $query = $this->_db_slave->query('SELECT FOUND_ROWS() AS `Count`');
        $total_rows = $query->row()->Count;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => array('rows' => $result, 'total_rows' => $total_rows));
        }
        return array('code' => 0);
    }

    public function tracking_banking($filter, $offset = 0, $limit = 10) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        if ($limit > 100)
            $limit = 100;
        $this->_db_slave->select(array('SQL_CALC_FOUND_ROWS deposit_banking.*,deposits.channel,deposits.scope_id'),FALSE);

        if (is_array($filter) === TRUE) {
            foreach ($filter as $kf => $vf) {
                $this->_db_slave->where("deposit_banking.$kf", $vf);
            }
        }
        $this->_db_slave->join('deposits','deposits.deposit_transaction = deposit_banking.deposit_transaction','left');
        $this->_db_slave->order_by('deposit_banking.id', 'desc');        
        $result = $this->_db_slave->get('deposit_banking', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        $query = $this->_db_slave->query('SELECT FOUND_ROWS() AS `Count`');
        $total_rows = $query->row()->Count;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => array('rows' => $result, 'total_rows' => $total_rows));            
        }
        return array('code' => 0);
    }

    public function new_credit($from, $to) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select('SUM(credit) AS mcoin, scope_id AS service_id, 1 AS partner_id', FALSE);
        $this->_db_slave->where('datetime_create >= ', $from . ' 00:00:00');
        $this->_db_slave->where('datetime_create <= ', $to . ' 23:59:59');
        $result = $this->_db_slave->get('deposits');
        $result = is_object($result) ? $result->result_array() : FALSE;
        //echo $this->_db_slave->last_query();
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => $result);
        } else {
            return array('code' => 0);
        }
    }

    public function history_card($from, $to) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(deposit_card.money) AS money', 'SUM(deposits.credit) AS credit', 'deposit_card.telco', 'deposits.scope_id AS service_id'));
        $this->_db_slave->join('deposits', 'deposits.deposit_transaction = deposit_card.deposit_transaction');
        $this->_db_slave->where('deposit_card.datetime_create >= ', $from . ' 00:00:00');
        $this->_db_slave->where('deposit_card.datetime_create <= ', $to . ' 23:59:59');
        $this->_db_slave->group_by(array('deposit_card.telco'));
        $result = $this->_db_slave->get('deposit_card');
        $result = is_object($result) ? $result->result_array() : FALSE;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => $result);
        } else {
            return array('code' => 0);
        }
    }

    public function history_sms($from, $to) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(deposit_sms.money) AS money', 'SUM(deposits.credit) AS credit', 'deposit_sms.telco', 'deposit_sms.service_number', 'deposits.scope_id AS service_id'));
        $this->_db_slave->join('deposits', 'deposits.deposit_transaction = deposit_sms.deposit_transaction');
        $this->_db_slave->where('deposit_sms.datetime_create >= ', $from . ' 00:00:00');
        $this->_db_slave->where('deposit_sms.datetime_create <= ', $to . ' 23:59:59');
        $this->_db_slave->group_by(array('deposit_sms.telco', 'deposit_sms.service_number'));
        $result = $this->_db_slave->get('deposit_sms');
        $result = is_object($result) ? $result->result_array() : FALSE;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => $result);
        } else {
            return array('code' => 0);
        }
    }

    public function history_bank($from, $to) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'deposit_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(deposit_banking.money) AS money', 'SUM(deposits.credit) AS credit', 'deposits.scope_id AS service_id'));
        $this->_db_slave->join('deposits', 'deposits.deposit_transaction = deposit_banking.deposit_transaction');
        $this->_db_slave->where('deposit_banking.datetime_create >= ', $from . ' 00:00:00');
        $this->_db_slave->where('deposit_banking.datetime_create <= ', $to . ' 23:59:59');
        $result = $this->_db_slave->get('deposit_banking');
        $result = is_object($result) ? $result->result_array() : FALSE;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => $result);
        } else {
            return array('code' => 0);
        }
    }

}
