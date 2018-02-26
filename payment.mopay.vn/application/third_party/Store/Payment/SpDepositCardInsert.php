<?php

class Store_Payment_SpDepositCardInsert extends Store_Processor {

    public $p_mobo_id;
    public $p_money;
    public $p_received_time;
    public $p_ip_called;
    public $p_ip_user;
    public $p_datetime_create;
    public $p_datetime_update;
    public $p_LANGUAGE;
    public $p_user_agent;
    public $p_platform;
    public $p_card_connection_id;
    public $p_telco;
    public $p_SERIAL;
    public $p_pin;
    public $p_card_id;
    public $p_STATUS;
    public $p_deposit_transaction;
    public $p_service_id;
    public $p_channel;
    public $p_access_token;
    public $p_game_info;

    protected $store_name = 'sp_deposit_card_insert';
    protected $object_name = __CLASS__;
}