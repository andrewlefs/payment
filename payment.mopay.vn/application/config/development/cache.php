<?php

$cache_file['monitor'] = array('path' => APPPATH . 'cache/monitors/');

$cache_mem['system_info'] = array(
    'cfg' => array('random' => FALSE),
    'data' => array(
        array('host' => '127.0.0.1', 'port' => '11211','ns'=>'graph_')
    )
);

$config['cache']['file'] = $cache_file;
$config['cache']['memcache'] = $cache_mem;
?>
