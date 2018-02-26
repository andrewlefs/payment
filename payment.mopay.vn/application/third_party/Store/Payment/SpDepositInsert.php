<?php

class Store_Payment_SpDepositInsert extends Store_Processor {

    public $p_mobo_id;
    public $p_money;
    public $p_credit;
    public $p_TYPE;
    public $p_blackbox_transaction;
    public $p_deposit_transaction;
    public $p_datetime_create;
    public $p_channel;
    public $p_provider;
    public $p_scope_id;

    protected $store_name = 'sp_deposit_insert';
    protected $object_name = __CLASS__;
}