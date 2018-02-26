<?php

class MEAPI_Config_Map {

    public static function getController() {
        return array(
            'payment' => 'MEAPI_Controller_PaymentController',
            'wallet' => 'MEAPI_Controller_WalletController',
            'blackbox' => 'MEAPI_Controller_BlackboxController',
            'deposit' => 'MEAPI_Controller_DepositController',
            'tracking' => 'MEAPI_Controller_TrackingController',
            'withdraw' => 'MEAPI_Controller_WithdrawController',
            'inside' => 'MEAPI_Controller_InsideController',
            'vcb' => 'MEAPI_Controller_VCBController',
            'adapter' => 'MEAPI_Controller_AdapterController'
        );
    }

    public static function getFunction() {
        return array(
            'payment_list' => 'get_payment_list',
            'payment_card' => 'verify_card',
            'payment_type' => 'get_payment_type',
            'payment_exchange' => 'get_payment_exchange',
            'verify_card' => 'verify_card'
        );
    }

}
