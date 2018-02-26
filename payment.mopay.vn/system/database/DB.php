<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Initialize the database
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 * @param 	array,string
 * @param 	bool	Determines if active record should be used or not
 */
function &DB($params = '', $active_record_override = NULL) {
    $active_record = TRUE;
    // Load the DB config file if a DSN string wasn't passed
    if (is_array($params)) {
        $CI = & get_instance();
        // Is the config file in the environment folder?
        if (!defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php')) {
            if (!file_exists($file_path = APPPATH . 'config/database.php')) {
                show_error('The configuration file database.php does not exist.');
            }
        }

        include($file_path);

        if (!isset($db) OR count($db) == 0) {
            show_error('No database connection settings were found in the database config file.');
        }

        if (!$params['type']) {
            $params['type'] = 'master';
        }
        $active_group = $params['db'];
        if (!isset($active_group) OR ! isset($db[$active_group])) {
            show_error('You have specified an invalid database connection group.');
        }
        $dbt = $db[$active_group];
        $CI->load->library('monitor');
        $CI->load->helper('random');
        if (strtoupper($params['type']) === 'MASTER') {
            checkMaster: {
                if ($dbt['cfg']['master_random'] == true) {
                    $fail = array();
                    getMaster: {
                        $rnd = randomExcept(0, $dbt['cfg']['master'] - 1, $fail);
                    }
                    if (is_numeric($rnd)) {
                        if ($CI->monitor->check_status('DATABASE', array('position' => $rnd, 'group' => $active_group)) === true) {
                            $db_position = $rnd;
                        } else {
                            $fail[] = $rnd;
                            goto getMaster;
                        }
                    }
                } else {
                    for ($i = 0; $i < $dbt['cfg']['master']; $i++) {
                        if ($CI->monitor->check_status('DATABASE', array('position' => $i, 'group' => $active_group)) === true) {
                            $db_position = $i;
                            break;
                        }
                    }
                }
            }
        } else {
            if ($dbt['cfg']['slave_random'] == true) {
                $fail = array();
                getSlave: {
                    $rnd = randomExcept($dbt['cfg']['master'], count($dbt['db']) - 1, $fail);
                }
                if (is_numeric($rnd)) {
                    if ($CI->monitor->check_status('DATABASE', array('position' => $rnd, 'group' => $active_group)) === true) {
                        $db_position = $rnd;
                    } else {
                        $fail[] = $rnd;
                        goto getSlave;
                    }
                }
            } else {
                for ($i = $dbt['cfg']['master'], $c = count($dbt['db']); $i < $c; $i++) {
                    if ($CI->monitor->check_status('DATABASE', array('position' => $i, 'group' => $active_group)) === true) {
                        $db_position = $i;
                        break;
                    }
                }
            }

            if (empty($db_position) === true) {
                goto checkMaster;
            }
        }
        if (is_numeric($db_position) === false) {
            $message = 'All database master fail !!!';
            include APPPATH . 'errors/error_system_maintenance.php';
            exit;
        }

        $params = $dbt['db'][$db_position];
    } elseif (is_string($params) AND strpos($params, '://') === TRUE) {

        /* parse the URL from the DSN string
         *  Database settings can be passed as discreet
         *  parameters or as a data source name in the first
         *  parameter. DSNs must have this prototype:
         *  $dsn = 'driver://username:password@hostname/database';
         */

        if (($dns = @parse_url($params)) === FALSE) {
            show_error('Invalid DB Connection String');
        }

        $params = array(
            'dbdriver' => $dns['scheme'],
            'hostname' => (isset($dns['host'])) ? rawurldecode($dns['host']) : '',
            'username' => (isset($dns['user'])) ? rawurldecode($dns['user']) : '',
            'password' => (isset($dns['pass'])) ? rawurldecode($dns['pass']) : '',
            'database' => (isset($dns['path'])) ? rawurldecode(substr($dns['path'], 1)) : ''
        );

        // were additional config items set?
        if (isset($dns['query'])) {
            parse_str($dns['query'], $extra);

            foreach ($extra as $key => $val) {
                // booleans please
                if (strtoupper($val) == "TRUE") {
                    $val = TRUE;
                } elseif (strtoupper($val) == "FALSE") {
                    $val = FALSE;
                }

                $params[$key] = $val;
            }
        }
    }

    // No DB specified yet?  Beat them senseless...
    if (!isset($params['dbdriver']) OR $params['dbdriver'] == '') {
        show_error('You have not selected a database type to connect to.');
    }

    // Load the DB classes.  Note: Since the active record class is optional
    // we need to dynamically create a class that extends proper parent class
    // based on whether we're using the active record class or not.
    // Kudos to Paul for discovering this clever use of eval()

    if ($active_record_override !== NULL) {
        $active_record = $active_record_override;
    }

    require_once(BASEPATH . 'database/DB_driver.php');

    if (!isset($active_record) OR $active_record == TRUE) {
        require_once(BASEPATH . 'database/DB_active_rec.php');

        if (!class_exists('CI_DB')) {
            eval('class CI_DB extends CI_DB_active_record { }');
        }
    } else {
        if (!class_exists('CI_DB')) {
            eval('class CI_DB extends CI_DB_driver { }');
        }
    }

    require_once(BASEPATH . 'database/drivers/' . $params['dbdriver'] . '/' . $params['dbdriver'] . '_driver.php');

    // Instantiate the DB adapter
    $driver = 'CI_DB_' . $params['dbdriver'] . '_driver';
    $DB = new $driver($params);
    if ($DB->autoinit == TRUE) {
        if ($DB->initialize() == false) {
            $CI->monitor->increment_fail('DATABASE', array('position' => $db_position, 'group' => $active_group));
            $heading = 'Database Error';
            $message = '<p>Unable to connect to your database server using the provided settings</p>';
            include APPPATH . 'errors/error_db.php';
            exit;
        }
    }
    if (isset($params['stricton']) && $params['stricton'] == TRUE) {
        $DB->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
    }

    return $DB;
}

// <editor-fold defaultstate="collapsed" desc="Function gen config database">
function gen_cfg_db($host, $username, $password, $database, $port = '3306', $db_debug = false, $dbdriver = 'mysqli') {
    $db['hostname'] = $host;
    $db['username'] = $username;
    $db['password'] = $password;
    $db['database'] = $database;
    $db['port'] = $port;
    $db['dbdriver'] = $dbdriver;
    $db['dbprefix'] = '';
    $db['pconnect'] = TRUE;
    $db['db_debug'] = $db_debug;
    $db['cache_on'] = FALSE;
    $db['cachedir'] = '';
    $db['char_set'] = 'utf8';
    $db['dbcollat'] = 'utf8_general_ci';
    $db['swap_pre'] = '';
    $db['autoinit'] = TRUE;
    $db['stricton'] = FALSE;
    return $db;
}

// </editor-fold>

/* End of file DB.php */
/* Location: ./system/database/DB.php */