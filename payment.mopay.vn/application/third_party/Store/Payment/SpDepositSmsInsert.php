<?php

class Store_Payment_SpDepositSmsInsert extends Store_Processor {

    public $p_mobo_id;
    public $p_sms_connection_id;
    public $p_mo_id;
    public $p_money;
    public $p_mo;
    public $p_mt;
    public $p_telco;
    public $p_phone;
    public $p_service_number;
    public $p_code;
    public $p_cdr;
    public $p_received_time;
    public $p_sms_transaction_id;
    public $p_ip_called;
    public $p_ip_user;
    public $p_datetime_create;
    public $p_datetime_update;
    public $p_language;
    public $p_user_agent;
    public $p_platform;
    public $p_deposit_transaction;
    public $p_status;

    protected $store_name = 'sp_deposit_sms_insert';
    protected $object_name = __CLASS__;
}