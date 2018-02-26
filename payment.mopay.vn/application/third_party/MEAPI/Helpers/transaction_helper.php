<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!function_exists('make_transaction')) {

    function make_transaction($prefix = NULL) {
        if ($prefix)
            $prefix .= '.';
        return $prefix . md5(uniqid($prefix, TRUE));
    }

}

if (!function_exists('gen_user_photo_path')) {

    function gen_user_photo_path($number) {
        $id = ($number % 10000000);

        $mil = intval($number / 10000000) * 10000000;
        $thos = intval($id / 10000) * 10000;
        return $mil . '/' . $thos . '/' . $id;
    }

}

if (!function_exists('make_mobo_service_id')) {

    function make_mobo_service_id() {
        return SERVICE_ID . base_convert(uniqid(), 16, 10);
    }

}
