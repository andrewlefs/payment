<?php

class Store_Payment_UpdateMtByMoId extends Store_Processor {

    public $p_sms_connection_id;
    public $p_mo_id;
    public $p_mt;
    public $p_cdr;

    protected $store_name = 'update_mt_by_mo_id';
    protected $object_name = __CLASS__;
}