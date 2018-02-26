<?php

if (!function_exists('format_phone')) {

    function format_phone($phone, $country_code = '84') {
        if (empty($phone) === TRUE)
            return false;
        $phone = str_replace('+', '', $phone);
        if ($phone[0] == '0') {
            ;
            $phone = $country_code . substr($phone, 1, strlen($phone));
        }
        return $phone;
    }

}
if (!function_exists('show_phone')) {

    function show_phone($phone) {
        if (substr($phone, 0, 2) == 84) {
            return '0' . substr($phone, 2);
        }
        return $phone;
    }

}
if (!function_exists('get_telco_by_phone')) {

    function get_telco_by_phone($phone) {
        $arrTelco = array(
            '8490' => 'mobifone', '8493' => 'mobifone', '84120' => 'mobifone', '84121' => 'mobifone', '84122' => 'mobifone', '84126' => 'mobifone', '84128' => 'mobifone',
            '8497' => 'viettel', '8498' => 'viettel', '84162' => 'viettel', '84163' => 'viettel', '84164' => 'viettel', '84165' => 'viettel', '84166' => 'viettel', '84167' => 'viettel', '84168' => 'viettel', '84169' => 'viettel',
            '8491' => 'vinaphone', '8494' => 'vinaphone', '84123' => 'vinaphone', '84124' => 'vinaphone', '84125' => 'vinaphone', '84127' => 'vinaphone', '84129' => 'vinaphone',
            '8492' => 'vietnammobile', '84188' => 'vietnammobile', '84186' => 'vietnammobile',
            '84996' => 'beeline', '84199' => 'beeline', '84993' => 'beeline', '8499' => 'beeline',
            '8495' => 'sfone',
            '8496' => 'viettel',
        );
        if (strlen($phone) == 11) {
            $prefix = substr($phone, 0, 4);
        } else {
            $prefix = substr($phone, 0, 5);
        }
        return $arrTelco[$prefix];
    }

}

if (!function_exists('is_phone')) {

    function is_phone($phone) {
        if (is_numeric($phone) == TRUE
                AND ( strlen($phone) == 11 OR strlen($phone) == 12)) {
            return TRUE;
        }
        return FALSE;
    }

}

if (!function_exists('is_phone_register')) {

    function is_phone_register($phone) {
        if (is_numeric($phone) == TRUE
                AND strlen($phone) >= 7) {
            return TRUE;
        }
        return FALSE;
    }

}

if (!function_exists('is_mobo_id')) {

    function is_mobo_id($id) {
        return strlen($id) == 9 ? TRUE : FALSE;
    }

}

if (!function_exists('is_mobo_service_id')) {

    function is_mobo_service_id($id) {
        return strlen($id) == 19 ? TRUE : FALSE;
    }

}