<?php
$ip_internal = array('115.78.161.124','192.168.1.5','14.161.5.226','14.161.5.226','118.69.76.21','113.161.77.69','113.161.78.101','118.69.76.212','115.78.161.88','203.162.56.175','123.30.140.179');

if(!in_array($_SERVER['REMOTE_ADDR'], $ip_internal)){
	die('You do not have permission to access this area');
}
set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

require_once 'curl.php';
require 'nusoap/nusoap.php';
$parse = parse_ini_file('config.ini', TRUE);

if(empty($parse) == TRUE){
	die('Setup links in config.ini with [production] section');
}

if(empty($parse['production']) == TRUE){
	die('ONLY ACCEPT [production],[database],[cache] section');
}