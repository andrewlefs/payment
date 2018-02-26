<?php

if (!function_exists('convert_provider')) {

    function convert_provider($provider, $app_id) {
        return $provider + ($app_id - 1) * 10000;
    }

}

if (!function_exists('revert_provider')) {

    function revert_provider($provider) {
        return $provider % 10000;
    }

}