<?php

class UserModel extends CI_Model {

    public $_db;
    public $_db_slave;
    private $CI;
    private $_tbl_account = 'accounts';
    private $_tbl_account_facebook = 'linked_facebook';
    private $_tbl_account_prefix = 'account_service_';

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function verify($phone, $password) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('phone', $phone);
        $this->_db_slave->where('password', $password);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get($this->_tbl_account, array('mobo_id', 'phone', 'facebook_id', 'fullname', 'status'));
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_access_token_by_mobo_id($mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('access_tokens');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_access_token_by_device($device_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('device_id', $device_id);
        $this->_db_slave->group_by('mobo_id');
        $this->_db_slave->order_by('id', 'DESC');
        $this->_db_slave->limit(5);
        $data = $this->_db_slave->get('access_tokens');
        return is_object($data) ? $data->result_array() : FALSE;
    }

    public function get_active_code($active_code, $mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->where('code', $active_code);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('active_code');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_active_code_by_phone($active_code, $phone) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('phone', $phone);
        $this->_db_slave->where('code', $active_code);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('active_code');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_raw_active_code($phone) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('phone', $phone);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('active_code');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function delete_active_code($mobo_id, $active_code) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        $this->_db->where('code', $active_code);
        return $this->_db->delete('active_code');
    }

    public function delete_active_code_by_phone($phone, $active_code) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('phone', $phone);
        $this->_db->where('code', $active_code);
        return $this->_db->delete('active_code');
    }

    public function insert_active_code($data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        return $this->_db->insert('active_code', $data);
    }

    public function insert_facebook($data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        return $this->_db->insert($this->_tbl_account_facebook, $data);
    }

    public function get_account_trial_by_device_id($device_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('mobo_id', 'temporary'));
        $this->_db_slave->where('device_id', $device_id);
        $this->_db_slave->where('(`phone` IS NULL OR `phone` = "" )', NULL, FALSE);
        $data = $this->_db_slave->get($this->_tbl_account);
        return is_object($data) ? $data->result_array() : FALSE;
    }

    public function get_account_by_device_id($device_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('mobo_id', 'temporary', 'phone', 'fullname'));
        $this->_db_slave->where('device_id', $device_id);
        $data = $this->_db_slave->get('accounts');
        return is_object($data) ? $data->result_array() : FALSE;
    }

    public function get_account_guest_by_device_id($device_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('mobo_id', 'phone', 'fullname'));
        $this->_db_slave->where('device_id', $device_id);
        $this->_db_slave->where('state', STATE_GUEST);
        $data = $this->_db_slave->get('accounts');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_user_by_phone($phone) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('accounts.*, linked_facebook.facebook_id'));
        $this->_db_slave->join('linked_facebook', $this->_tbl_account_facebook . '.mobo_id = ' . $this->_tbl_account . '.mobo_id', 'LEFT');
        $this->_db_slave->where('phone', $phone);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get($this->_tbl_account);
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_account_by_fb_id($facebook_id, $facebook_token = NULL) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->select(array('accounts.*, linked_facebook.facebook_id, linked_facebook.facebook_token'));
        $this->_db_slave->join('accounts', $this->_tbl_account_facebook . '.mobo_id = ' . $this->_tbl_account . '.mobo_id', 'LEFT');
        $this->_db_slave->where($this->_tbl_account_facebook . '.facebook_id', $facebook_id);
        if (empty($facebook_token) == FALSE) {
            $this->_db_slave->or_where($this->_tbl_account_facebook . '.facebook_token', $facebook_token);
        }
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get($this->_tbl_account_facebook);
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function update_facebook_token($data_update, $facebook_id) {
        if (!$this->_db) {
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        }
        $this->_db->where('facebook_id', $facebook_id);
        return $this->_db->update($this->_tbl_account_facebook, $data_update);
    }

    public function map_account_facebook($mobo_id, $facebook_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->update($this->_tbl_account_facebook, array('facebook_id' => $facebook_id));
    }

    public function get_user_by_access_token($access_token) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('access_token', $access_token);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('access_tokens');
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function insert_service($service_id, $data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $result = $this->_db->insert("account_service_{$service_id}", $data);
        return empty($result) == FALSE ? $this->_db->insert_id() : FALSE;
    }

    public function get_service_by_mobo_id($service_id, $mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->select('mobo_id,mobo_service_id,fullname,device_id,channel');
        $this->_db_slave->where('mobo_id', $mobo_id);
        //$this->_db_slave->limit(1);
        $data = $this->_db_slave->get("account_service_$service_id");
        return is_object($data) ? $data->result_array() : FALSE;
    }

    public function get_user_by_mobo_id($mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('accounts.*, linked_facebook.facebook_id'));
        $this->_db_slave->join('linked_facebook', $this->_tbl_account_facebook . '.mobo_id = ' . $this->_tbl_account . '.mobo_id', 'LEFT');
        $this->_db_slave->where('accounts.mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get($this->_tbl_account);
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function get_user_by_mobo_service_id($app_id, $mobo_service_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select(array('accounts.*, linked_facebook.facebook_id'));
        $this->_db_slave->join('linked_facebook', $this->_tbl_account_facebook . '.mobo_id = ' . $this->_tbl_account . '.mobo_id', 'LEFT');
        $this->_db_slave->join("account_service_$app_id", "account_service_$app_id.mobo_id=" . $this->_tbl_account . '.mobo_id', 'LEFT');
        $this->_db_slave->where("account_service_$app_id.mobo_service_id", $mobo_service_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get($this->_tbl_account);
        //echo $this->_db_slave->last_query();
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function update_password($mobo_id, $password) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->update($this->_tbl_account, array('password' => $password));
    }

    public function update_info($mobo_id, $data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->update($this->_tbl_account, $data);
    }

    public function update_info_facebook($facebook_id, $data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('facebook_id', $facebook_id);
        return $this->_db->update($this->_tbl_account_facebook, $data);
    }

    public function update_last_login_time($service_id, $mobo_service_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $data_update['last_login_time'] = date('Y-m-d H:i:s');
        $this->_db->where('mobo_service_id', $mobo_service_id);
        return $this->_db->update('account_service_' . $service_id, $data_update);
    }

    public function get_info($mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->select("{$this->_tbl_account}.*, {$this->_tbl_account_facebook}.facebook_id");
        $this->_db_slave->join('linked_facebook', $this->_tbl_account_facebook . '.mobo_id = ' . $this->_tbl_account . '.mobo_id', 'LEFT');
        $this->_db_slave->where($this->_tbl_account . '.mobo_id', $mobo_id);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get($this->_tbl_account);
        return is_object($data) ? $data->row_array() : FALSE;
    }

    public function register_quickly($params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $params['service_create'] = SERVICE_ID;
        $result = StoreProcedure::call('SP_InsertUser', $params, $this->_db);
        if (empty($result) == FALSE) {
            $data = $result->row_array();
            $mobo_id = $data['mobo_id'];
        }
        return empty($result) == TRUE ? FALSE : $mobo_id;
    }

    public function register_mobo_quickly($params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $params['service_create'] = SERVICE_ID;
        $result = StoreProcedure::call('SP_InsertUserMobo', $params, $this->_db);
        if (empty($result) == FALSE) {
            $data = $result->row_array();
            $mobo_id = $data['mobo_id'];
        }
        return empty($result) == TRUE ? FALSE : $mobo_id;
    }

    public function register($params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $params['service_create'] = SERVICE_ID;
        $result = StoreProcedure::call('SP_InsertUserWithPassword', $params, $this->_db);
        if (empty($result) == FALSE) {
            $data = $result->row_array();
            $mobo_id = $data['mobo_id'];
        }
        return empty($result) == TRUE ? FALSE : $mobo_id;
    }

    public function register_guest($params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $params['service_create'] = SERVICE_ID;
        $result = StoreProcedure::call('SP_InsertUserGuest', $params, $this->_db);
        if (empty($result) == FALSE) {
            $data = $result->row_array();
            $mobo_id = $data['mobo_id'];
        }
        return empty($result) == TRUE ? FALSE : $mobo_id;
    }

    public function update($user_id, $params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), true);
        $this->_db->where('mobo_id', $user_id);
        $this->_db->limit(1);
        $this->_db->update($this->_tbl_account, $params);
        return $this->_db->affected_rows();
    }

    public function register_access_token($params = array()) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->insert('access_tokens', $params);
        return $this->_db->insert_id();
    }

    public function verify_access_token($access_token) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('access_token', $access_token);
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('access_tokens');
        if (is_object($data))
            return $data->row_array();
    }

    public function delete_access_token_by_mobo_id($mobo_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->delete('access_tokens');
    }

    public function delete_user_by_mobo_id($mobo_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->delete($this->_tbl_account);
    }

    public function delete_facebook_by_mobo_id($mobo_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->delete('linked_facebook');
    }

    public function delete_facebook_by_fb_id($facebook_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('facebook_id', $facebook_id);
        return $this->_db->delete($this->_tbl_account_facebook);
    }

    public function delete_active_code_by_mobo_id($mobo_id) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        $this->_db->where('mobo_id', $mobo_id);
        return $this->_db->delete('active_code');
    }

    public function insert_loop($func, $params) {
        $this->CI->load->MEAPI_Library('StoreProcedure');
        $result = StoreProcedure::call($func, $params, $this->_db);

        $i = 0;
        while (is_object($result) == FALSE OR $result->row_array() == FALSE) {
            $result = StoreProcedure::call($func, $params, $this->_db);
            if ($i > 100) {
                break;
            }
            $i++;
        }

        $data = array();
        if (is_object($result) == TRUE) {
            $data = $result->row_array();
        }

        if ($i > 0) {
            $agrs['mobo_id'] = empty($data['mobo_id']) == TRUE ? NULL : $data['mobo_id'];
            $agrs['phone'] = empty($params['temporary']) == TRUE ? $params['phone'] : $params['temporary'];
            $agrs['device_id'] = $params['device_id'];
            $agrs['fail'] = $i;
            StoreProcedure::call('SP_InsertUserError', $agrs, $this->_db);
        }
        return $data;
    }

    public function get_mobo_service($service_id, $mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);
        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->order_by('last_login_time DESC');
        $this->_db_slave->limit(1);
        $data = $this->_db_slave->get('account_service_' . $service_id);
        if (is_object($data))
            return $data->row_array();
    }

    public function insert_mobo_service($service_id, $data) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);
        return $this->_db->insert('account_service_' . $service_id, $data);
    }

    public function get_all_app() {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->select(array('app', 'service_id'));
        $result = $this->_db_slave->get('app');
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function get_account_active($mobo_id) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->where('mobo_id', $mobo_id);
        $this->_db_slave->where('status', 1);
        $result = $this->_db_slave->get('account_mobo_giftcode');
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function get_giftcode($giftcode) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->where('giftcode', $giftcode);
        $result = $this->_db_slave->get('account_mobo_giftcode');
        return is_object($result) ? $result->row_array() : FALSE;
    }

    public function get_account_active_giftcode($phone) {
        if (!$this->_db_slave)
            $this->_db_slave = $this->load->database(array('db' => 'user_info', 'type' => 'slave'), TRUE);

        $this->_db_slave->where('phone', $phone);
        $result = $this->_db_slave->get('account_mobo_giftcode');
        return is_object($result) ? $result->result_array() : FALSE;
    }

    public function update_giftcode($data, $giftcode) {
        if (!$this->_db)
            $this->_db = $this->load->database(array('db' => 'user_info', 'type' => 'master'), TRUE);

        $this->_db->where('giftcode', $giftcode);
        return $this->_db->update('account_mobo_giftcode', $data);
    }

}
