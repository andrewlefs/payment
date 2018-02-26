<?php

class Store_Wallet_SpWalletRequestsInsert extends Store_Processor {

    public $p_blackbox_transaction;
    public $p_status;
    public $p_mobo_id;
    public $p_type;
    public $p_datetime_create;

    protected $store_name = 'sp_wallet_requests_insert';
    protected $object_name = __CLASS__;
}