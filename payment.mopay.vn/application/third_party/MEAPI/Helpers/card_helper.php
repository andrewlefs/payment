<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!function_exists('validate_card')) {

    function validate_card($serial, $pin, $card) {
        $config = array(
            'vms' => array(
                array('pin' => 12, 'serial' => 11, 'pattern' => '/[^0-9]/i'),
                array('pin' => 12, 'serial' => 12, 'pattern' => '/[^0-9]/i'),
                array('pin' => 12, 'serial' => 13, 'pattern' => '/[^0-9]/i'),
                array('pin' => 12, 'serial' => 14, 'pattern' => '/[^0-9]/i'),
                array('pin' => 12, 'serial' => 15, 'pattern' => '/[^0-9]/i'),
                array('pin' => 12, 'serial' => 16, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 11, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 12, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 13, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 14, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 15, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 9, 'pattern' => '/[^0-9]/i'),
            ),
            'gate' => array(
                array('pin' => 10, 'serial' => 10, 'pattern' => '/[^A-Za-z0-9_\-]/i')
            ),
            'vcoin' => array(
                array('pin' => 12, 'serial' => 12, 'pattern' => '/[^A-Za-z0-9_\-]/i')
            ),
            'viettel' => array(
                array('pin' => 13, 'serial' => 11, 'pattern' => '/[^0-9]/i'),
                array('pin' => 14, 'serial' => 11, 'pattern' => '/[^0-9]/i'),
				array('pin' => 15, 'serial' => 14, 'pattern' => '/[^0-9]/i'),
            ),
            'vina' => array(
                array('pin' => 12, 'serial' => 9, 'pattern' => '/[^A-Za-z0-9_\-]/i'),
                array('pin' => 14, 'serial' => 9, 'pattern' => '/[^A-Za-z0-9_\-]/i'),
                array('pin' => 14, 'serial' => 11, 'pattern' => '/[^A-Za-z0-9_\-]/i'),
                array('pin' => 14, 'serial' => 14, 'pattern' => '/[^A-Za-z0-9_\-]/i'),
            ),
            'gate' => array(
                array('pin' => 10, 'serial' => 10, 'pattern' => '/[^A-Za-z0-9_\-]/i')
            ),
        );
        if ($serial && !preg_match('/[^A-Za-z0-9_\-]/i', $serial)) {
            if ($pin) {
                if ($config[$card]) {
                    foreach ($config[$card] as $row) {
                        if ($row['pin'] == strlen($pin)) {
                            $validate_pin = true;
                            if ($row['serial'] == strlen($serial)) {
                                $validate_serial = true;
                                if (!preg_match($row['pattern'], $pin)) {
                                    return true;
                                }
                            }
                        }
                    }
                }else{
                    return FALSE;
                }
                if ($validate_pin !== true) {
                    return FALSE;
                } elseif ($validate_serial !== true) {
                    return FALSE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
        return TRUE;
    }

}
