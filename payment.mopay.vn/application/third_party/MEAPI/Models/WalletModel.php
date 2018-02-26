<?php

class WalletModel extends CI_Model {

    public $_db;
    public $_db_slave;
    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->helper('utils');
    }

    public function insert_wallet_request($blackbox_transaction, $mobo_id, $type) {
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        $store = new Store_Wallet_SpWalletRequestsInsert();
        $store->p_blackbox_transaction = $blackbox_transaction;
        $store->p_mobo_id = $mobo_id;
        $store->p_status = FALSE;
        $store->p_type = $type;
        $objResult = $store->execute($this->_db);
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

    public function deposit($blackbox_transaction, $mobo_id, $credit) {
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_wallets_action', array($blackbox_transaction, $mobo_id, intval($credit), 'deposit'), $this->_db);
        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array(
                    'code' => 1,
                    'balance' => $result['CurrCredit']
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

    public function withdraw($blackbox_transaction, $mobo_id, $credit) {
        if (empty($this->_db) == TRUE) {
            $this->_db = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        $objResult = StoreProcedure::call('sp_wallets_action', array($blackbox_transaction, $mobo_id, intval($credit), 'withdraw'), $this->_db);
        if (empty($objResult) === FALSE) {
            $result = $objResult->row_array();
            if ($result['Errcode'] == 0) {
                return array(
                    'code' => 1,
                    'balance' => $result['CurrCredit']
                );
            } elseif ($result['Errcode'] == 88888) {
                return array(
                    'code' => -1 // The balance is not enough
                );
            } elseif ($result['Errcode'] == 99999) {
                return array(
                    'code' => -2 // This account does not exist
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

    public function balance($mobo_id) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->where('mobo_id', $mobo_id);
        $result = $this->_db_slave->get('wallets');
        $result = is_object($result) ? $result->row_array() : FALSE;		
        if (empty($result) === FALSE) {
            return array(
                'balance' => $result['credit'],
                'last_update' => $result['datetime_update']
            );
        } else {
            return array(
                'balance' => 0,
                'last_update' => NULL
            );
        }
    }

    public function top($filter_type = NULL, $offset = 0, $limit = 10, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        if ($limit > 100)
            $limit = 100;
        $this->_db_slave->select(array('SUM(credit) AS credit', 'mobo_id'));
        $this->_db_slave->order_by('credit', 'desc');
        if (empty($filter_type) === FALSE) {
            if ($filter_type == 'wallet') {
                $result = $this->_db_slave->get('wallets', $limit, $offset);
            } else {
                if (empty($from) === FALSE && empty($to) === FALSE) {
                    $this->_db_slave->where('datetime_create >= ', format_from_date($from) );
                    $this->_db_slave->where('datetime_create <= ', format_to_date($to) );
                }
                $this->_db_slave->where('type', $filter_type);
                $this->_db_slave->group_by('mobo_id');
                $result = $this->_db_slave->get('wallet_historys', $limit, $offset);
            }

        }
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

    public function report() {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SUM(credit) AS credit'));
        $result = $this->_db_slave->get('wallets');
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

    public function history($mobo_id, $offset = 0, $limit = 10, $from = NULL, $to = NULL) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'wallet_info', 'type' => 'master'), TRUE);
        }
        $this->_db_slave->select(array('SQL_CALC_FOUND_ROWS *','credit', 'type', 'datetime_create'),FALSE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        if (empty($from) === FALSE && empty($to) === FALSE) {
            $this->_db_slave->where('datetime_create >= ', $from . ' 00:00:00');
            $this->_db_slave->where('datetime_create <= ', $to . ' 23:59:59');
        }
        $this->_db_slave->order_by("datetime_create", "desc");
        $result = $this->_db_slave->get('wallet_historys', $limit, $offset);
        $result = is_object($result) ? $result->result_array() : FALSE;
        $query = $this->_db_slave->query('SELECT FOUND_ROWS() AS `Count`');
        $total_rows = $query->row()->Count;
        if (empty($result) === FALSE) {
            return array('code' => 1, 'detail' => array('rows' => $result, 'total_rows' => $total_rows));
        } else {
            return array('code' => 0);
        }
    }

}
