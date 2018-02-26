<?php

class Store_Payment_SpSmsTransactionsInsert extends Store_Processor {

    public $p_datetime_create;
    public $p_language;
    public $p_user_agent;
    public $p_platform;
    public $p_sms_transaction_id;
    public $p_mobo_id;
    public $p_service_number;
    public $p_ip;
    public $p_scope_id;

    protected $store_name = 'sp_sms_transactions_insert';
    protected $object_name = __CLASS__;
}