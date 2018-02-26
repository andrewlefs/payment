<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

$db['system_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays'),
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays')
    )
);

$db['user_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.8.13.82', 'trinm', 'btWR2Vp37vDTT9DN', 'mopays'),
        gen_cfg_db('10.8.13.82', 'trinm', 'btWR2Vp37vDTT9DN', 'mopays')
    )
);

$db['payment_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.8.13.82', 'trinm', 'btWR2Vp37vDTT9DN', 'mopays'),
        gen_cfg_db('10.8.13.82', 'trinm', 'btWR2Vp37vDTT9DN', 'mopays')
    )
);

$db['deposit_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays'),
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays')
    )
);

$db['withdraw_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays'),
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays')
    )
);

$db['wallet_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays'),
        gen_cfg_db('127.0.0.1', 'root', '', 'mopays')
    )
);


/* End of file database.php */
/* Location: ./application/config/database.php */