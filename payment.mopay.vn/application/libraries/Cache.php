<?php

class Cache {

    private $CI;
    private $cfg_cache;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->config->load('cache');
        $this->cfg_cache = & $this->CI->config->item('cache');
    }

    public function load($cache_type, $group_name, $options = array()) {
        $cfg_cache = $this->cfg_cache[$cache_type][$group_name];
        if (empty($cfg_cache) == TRUE)
            return false;
        switch ($cache_type) {
            case 'file':
                $obj_cache_name = 'MY' . $cache_type . $group_name;
                $this->CI->load->driver('cache', array('adapter' => 'file', 'params' => array('obj' => $obj_cache_name, 'path' => $cfg_cache['path'])), $obj_cache_name);
                return $this->CI->$obj_cache_name;
                break;
            case 'memcache':
                if (is_array($cfg_cache) === FALSE) {
                    log_message('error', 'The Memcached config is not array');
                    exit;
                }
                $this->CI->load->library('monitor');
                $random = FALSE;
                if ($cfg_cache['cfg']['random'] == TRUE)
                    $random = TRUE;
                if ($random === TRUE) {
                    $this->CI->load->helper('random');
                    $fail = array();
                    getMemcache: {
                        $rnd = randomExcept(0, count($cfg_cache['data']) - 1, $fail);
                    }
                    if (is_numeric($rnd)) {
                        if ($this->CI->monitor->check_status('MEMCACHE', array('position' => $rnd, 'group' => $group_name)) === true) {
                            $cache_position = $rnd;
                        } else {
                            $fail[] = $rnd;
                            goto getMemcache;
                        }
                    }
                } else {
                    for ($i = 0, $c = count($cfg_cache['data']); $i < $c; $i++) {
                        if ($this->CI->monitor->check_status('MEMCACHE', array('position' => $i, 'group' => $group_name)) === true) {
                            $cache_position = $i;
                            break;
                        }
                    }
                }
                if ($cfg_cache['data'][$cache_position]) {
                    $memcfg['host'] = $cfg_cache['data'][$cache_position]['host'];
                    $memcfg['port'] = $cfg_cache['data'][$cache_position]['port'];
                    $memcfg['ns'] = $cfg_cache['data'][$cache_position]['ns'];
                    $status = TRUE;
                } else {
                    $status = FALSE;
                }
                $obj_cache_name = 'MY' . $cache_type . $group_name;
                $this->CI->load->driver('cache', array('adapter' => 'memcached', 'params' => array('obj' => $obj_cache_name, 'hostname' => $memcfg['host'], 'port' => $memcfg['port'], 'status' => $status,'ns'=>$memcfg['ns'])), $obj_cache_name);
                if ($this->CI->{$obj_cache_name}->check() === FALSE) {
                    $this->CI->monitor->increment_fail('MEMCACHE', array('position' => $cache_position));
                }
                unset($memcfg);
                return $this->CI->$obj_cache_name;
                break;
            default:
                break;
        }
    }

}

?>
