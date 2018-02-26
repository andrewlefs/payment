<?php

class MEAPI_Config_ResponseCode {

    public static function getCode() {
        return array(
            'AUTHORIZE_FAIL' => -7,
            'SYSTEM_ERROR' => -6,
            'INVALID_PARAMS' => -5,
            'INVALID_SCOPE' => -3,
            'INVALID_TOKEN' => -1,
            'NOT_PERMISSION_APP' => -4,
            'INVALID_CARD' => -5,
            'ACCOUNT_NOT_ACTIVE' => -9,
            'ACCOUNT_EXIST' => 100,
            'ACCOUNT_NOT_EXIST' => 101,
            'BLACKBOX_TRANSACTION_NOT_EXIST' => 102,
            'BALANCE_NOT_ENOUGHT' => 103,
            'REQUEST_SUCCESS' => 110,
            'REQUEST_FAIL' => 111,
            /*
             * Response Code for VCB, Start from number 1, Length 4
             */
            'TOPUP_SUCCESS' => 1000,
            'TOPUP_FAIL' => 1001,

            'ACTIVE_SUCCESS' => 1100,
            'ACTIVE_FAIL' => 1101,
            'ACTIVE_EXIST' => 1102,
            'CHECK_SUCCESS' => 1103,
            'CHECK_FAIL' => 1104,
            'VERIFY_ACTIVE_SUCCESS' => 1110,
            'VERIFY_ACTIVE_FAIL' => 1111,
            'VERIFY_ACTIVE_INVALID' => 1112,

            'GET_CART_SUCCESS' => 1200,
            'CART_NOT_EXIST' => 1201,
            'CART_EXPIRED' => 1202,
            'CART_PAID' => 1203,

            'CHECKOUT_SUCCESS' => 1300,
            'CHECKOUT_FAIL' => 1301,

            'DEACTIVE_SUCCESS' => 1400,
            'DEACTIVE_FAIL' => 1401,

            'GET_ACTIVE_LIST_SUCCESS' => 1500,
            'GET_ACTIVE_LIST_EMPTY' => 1502,
            'GET_ACTIVE_LIST_FAIL' => 1501,

            'SEND_OTP_SUCCESS' => 1600,
            'SEND_OTP_FAIL' => 1601,

        );
    }

}