<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

$db['system_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
		gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
    )
);
//gen_cfg_db('10.10.35.131', 'memopayvn', 'oiA82DdgA63126f', 'payment_mopay_vn'),
$db['user_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
        gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
    )
);

$db['payment_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
		gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
    )
);
//gen_cfg_db('10.10.35.131', 'memopayvn', 'oiA82DdgA63126f', 'payment_mopay_vn'),
$db['deposit_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
		gen_cfg_db('localhost', 'root', '', 'payment_mopay_vn'),
    )
);

$db['withdraw_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.35.131', 'memopayvn', 'oiA82DdgA63126f', 'payment_mopay_vn'),
		gen_cfg_db('10.10.35.131', 'memopayvn', 'oiA82DdgA63126f', 'payment_mopay_vn'),
    )
);

$db['wallet_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.35.131', 'memopayvn', 'oiA82DdgA63126f', 'wallet_mopay_vn'),
		gen_cfg_db('10.10.35.131', 'memopayvn', 'oiA82DdgA63126f', 'wallet_mopay_vn'),
    )
);

$db['service_info'] = array(
    'cfg' => array('master' => 1, 'master_random' => false, 'slave_random' => false),
    'db' => array(
        gen_cfg_db('10.10.20.158', 'ttkt', 'dsmmpp99sk', 'service_mobo_vn', '3306',false, 'mysqli'),
        gen_cfg_db('10.10.20.158', 'ttkt', 'dsmmpp99sk', 'service_mobo_vn', '3306',false, 'mysqli')
    )
);


/* End of file database.php */
/* Location: ./application/config/database.php */