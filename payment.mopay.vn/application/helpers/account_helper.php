<?php

if (!function_exists('validate_username')) {

    function validate_username($username) {
        if (preg_match('/[^A-Za-z0-9]/i', $username)) {
            return 'USERNAME_NOT_ALLOW_SPECIAL_CHARACTERS';
        } elseif (strlen($username) < 5 || strlen($username) > 20) {
            return 'USERNAME_LENGTH_INVALID';
        } else {
            $arrFilter = array('admin', 'admjn', 'tongdai', '19006611');
            for ($i = 0, $c = count($arrFilter); $i < $c; $i++) {
                if (preg_match('/' . $arrFilter[$i] . '/i', $username)) {
                    return 'USERNAME_FORBIDDEN';
                }
            }
            return TRUE;
        }
        return TRUE;
    }

}

if (!function_exists('validate_account')) {

    function validate_account($username, $password, $flag_valid_username = TRUE) {
        if ($flag_valid_username === TRUE)
            $valid_username = validate_username($username);
        if ($valid_username !== TRUE && $flag_valid_username === TRUE)
            return $valid_username;
        if (strlen($password) < 4 || strlen($password) > 20) {
            return 'PASSWORD_LENGTH_INVALID';
        } else {
            $arrFilter = array('admin', 'admjn', 'tongdai', '19006611');
            for ($i = 0, $c = count($arrFilter); $i < $c; $i++) {
                if (preg_match('/' . $arrFilter[$i] . '/i', $password)) {
                    return 'PASSWORD_FORBIDDEN';
                }
            }
            if (@preg_match('/' . $username . '/i', $password) || @preg_match('/' . $password . '/i', $username)) {
                return 'PASSWORD_CONTAINS_USERNAME';
            }
            return TRUE;
        }
        return TRUE;
    }

}
?>
