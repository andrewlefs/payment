<?php

class Monitor {

    private $CI;
    public $max_count_fail = 5;
    public $time_store_fail = 1;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->library('cache');
    }

    public function increment_fail($type, $options = array()) {
        if ($this->_check_type($type)) {
            $cache_monitor = $this->CI->cache->load('file', 'monitor');
            $key_monitor = 'TEMP_' . $type . '_' . $options['group'] . '_' . $options['position'];
            $count_fail = intval($cache_monitor->get($key_monitor)) + 1;
            if ($count_fail > $this->max_count_fail) {
                $key_monitor_status = 'STATUS_' . $type . '_' . $options['group'] . '_' . $options['position'];
                $this->_alert_disable($type, $options);
                $cache_monitor->save($key_monitor_status, json_encode(array('TYPE' => $type, 'GROUP' => $options['group'], 'POSITION' => $options['position'], 'STATUS' => 'disable')), 3600);
            } else {
                $this->_alert_fail($type, $options);
                $cache_monitor->save($key_monitor, $count_fail, $this->time_store_fail);
            }
        }
    }

    public function check_status($type, $options = array()) {
        if ($this->_check_type($type)) {
            $cache_monitor = $this->CI->cache->load('file', 'monitor');
            $key_monitor_status = 'STATUS_' . $type . '_' . $options['group'] . '_' . $options['position'];
            $result = $cache_monitor->get($key_monitor_status);
            if (empty($result) === FALSE)
                $result = json_decode($result, TRUE);
            if ($result['STATUS'] == 'disable') {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    private function _alert_fail($type, $options = array()) {
        MEAPI_Log::writeCsv(array($type, $options['group'], $options['position'], 'CONNECT FAIL'), date('d-m-Y'), 'monitor', NULL);
        return true;
    }

    private function _alert_disable($type, $options = array()) {
        MEAPI_Log::writeCsv(array($type, $options['group'], $options['position'], 'DISABLE'), date('d-m-Y'), 'monitor', NULL);
        return true;
    }

    private function _check_type($type) {
        if (!in_array(strtoupper($type), array('MEMCACHE', 'DATABASE', 'COUCHBASE'))) {
            echo 'Only allow DATABASE or MEMCACHE';
            exit;
        }
        return true;
    }

}

?>
