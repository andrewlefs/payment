<?php

$cache_file['monitor'] = array('path' => APPPATH . 'cache/monitors/');

$cache_mem['system_info'] = array(
    'cfg' => array('random' => FALSE),
    'data' => array(
        array('host' => '10.10.35.131', 'port' => '11211','ns'=>'PAYMENT_')
    )
);

$cache_mem['mopay_info'] = array(
    'cfg' => array('random' => FALSE),
    'data' => array(
        array('host' => '10.10.35.131', 'port' => '11211','ns'=>'PAYMENT_')
    )
);

$config['cache']['file'] = $cache_file;
$config['cache']['memcache'] = $cache_mem;
?>
