<?php

class Store_Payment_CheckMoId extends Store_Processor {

    public $p_sms_connection_id;
    public $p_mo_id;

    protected $store_name = 'check_mo_id';
    protected $object_name = __CLASS__;
}