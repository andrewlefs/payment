<?php

class CouchBaseInterface {

    public function __call($name, $arguments) {
        
    }

}

class CouchBaseDB {

    /**
     *
     * @var CI_Controller
     */
    private $CI;
    private $cfg;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->config->load('couchbase');
        $this->cfg = & $this->CI->config->item('couchbase');
        $this->CI->load->library('monitor');
    }

    public function load($group_name = 'default') {
        $obj_couchbase_name = 'MYCOUCHBASE' . $group_name;
        if ($this->{$obj_couchbase_name}) {
            return $this->{$obj_couchbase_name};
        }
        $cfg = $this->cfg[$group_name];
        $this->CI->load->library('monitor');
        $random = FALSE;

        if (is_array($cfg) === FALSE) {
            log_message('error', 'The Memcached config is not array');
            exit;
        }
        $this->CI->load->library('monitor');
        $random = FALSE;
        if ($cfg['cfg']['random'] == TRUE)
            $random = TRUE;
        if ($random === TRUE) {
            $this->CI->load->helper('random');
            $fail = array();
            getCouchbase: {
                $rnd = randomExcept(0, count($cfg['data']) - 1, $fail);
            }
            if (is_numeric($rnd)) {
                if ($this->CI->monitor->check_status('COUCHBASE', array('position' => $rnd, 'group' => $group_name)) === true) {
                    $couchbase_position = $rnd;
                } else {
                    $fail[] = $rnd;
                    goto getCouchbase;
                }
            }
        } else {
            for ($i = 0, $c = count($cfg['data']); $i < $c; $i++) {
                if ($this->CI->monitor->check_status('COUCHBASE', array('position' => $i, 'group' => $group_name)) === true) {
                    $couchbase_position = $i;
                    break;
                }
            }
        }

        if ($cfg['data'][$couchbase_position]) {
            $cbcfg = $cfg['data'][$couchbase_position];
            $status = TRUE;
        } else {
            $status = FALSE;
        }

        if (is_array($cbcfg)) {
            $cb = new Couchbase($cbcfg['ip'], $cbcfg['username'], $cbcfg['password'], $cbcfg['db_name']);
            if ($cb->getResultCode() != COUCHBASE_SUCCESS) {
                $this->CI->monitor->increment_fail('COUCHBASE', array('position' => $couchbase_position, 'group' => $group_name));
                $cb = new CouchBaseInterface();
            }
            $this->{$obj_couchbase_name} = $cb;
        } else {
            $cb = new CouchBaseInterface();
        }
        return $cb;
    }

}

?>
