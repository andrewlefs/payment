<?php

class Store_Payment_SpDepositBankInsert extends Store_Processor {

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
    public $p_bank_connection_id;
    public $p_bank_type;
    public $p_bank_code;
    public $p_bank_transaction;
    public $p_STATUS;
    public $p_deposit_transaction;

    protected $store_name = 'sp_deposit_bank_insert';
    protected $object_name = __CLASS__;
}