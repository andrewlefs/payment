<?php

class API_Service_Payment_Input {

    public $mobo_id;
    public $mobo_service_id;
    public $transaction;
    public $money;
    public $credit;
    public $payment_type;
    public $game_info;

}

class template_service {

    public function __construct() {
    }

    public function recharge(API_Service_Payment_Input $input) {
        
    }
}