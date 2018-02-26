<?php

class Store_Payment_SpWithdrawHistorysInsert extends Store_Processor {

    public $p_mobo_id;
    public $p_credit;
    public $p_datetime_create;
    public $p_withdraw_transaction;
    public $p_STATUS;
    public $p_ip_called;
    public $p_ip_user;
    public $p_datetime_update;
    public $p_LANGUAGE;
    public $p_user_agent;
    public $p_platform;

    protected $store_name = 'sp_withdraw_historys_insert';
    protected $object_name = __CLASS__;
}