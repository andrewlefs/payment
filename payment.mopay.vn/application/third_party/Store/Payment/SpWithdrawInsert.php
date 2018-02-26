<?php

class Store_Payment_SpWithdrawInsert extends Store_Processor {

    public $p_mobo_id;
    public $p_blackbox_transaction;
    public $p_withdraw_transaction;
    public $p_datetime_create;
    public $p_credit;
    public $p_verify;
    public $p_result_items;
    public $p_channel;
    public $p_provider;
    public $p_scope_id;
    public $p_service_id;

    protected $store_name = 'sp_withdraw_insert';
    protected $object_name = __CLASS__;
}