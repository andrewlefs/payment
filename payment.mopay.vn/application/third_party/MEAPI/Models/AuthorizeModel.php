<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizeModel
 *
 * @author vudn
 */
class AuthorizeModel extends CI_Model {

    public $_db;
    public $_db_slave;

    public function __construct() {
        $this->load->MEAPI_Library('StoreProcedure');
    }
    
    public function getAuthorize($params) {
        if (empty($this->_db_slave) == TRUE) {
            $this->_db_slave = $this->load->database(array('db' => 'system_info', 'type' => 'slave'), TRUE);
        }
        $result = StoreProcedure::call('SP_AuthorizeGetPermission', $params, $this->_db_slave);                
        if ($result) {
            $output = $result->result_array();
        } else {
            $output = array();
        }
        return $output;
    }
}
