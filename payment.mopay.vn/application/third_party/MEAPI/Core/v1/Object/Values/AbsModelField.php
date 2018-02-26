<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Object\Values;

use Misc\Object\AbstractObject;

class AbsModelField extends AbstractObject {

    const defaultPath = "m_payment";
    const NAME = "m_models";
    const TABLE_PAYMENT_LOG_PURCHASE = "payment_log_purchase";

    public function getPath() {
//        $documentRoot = $_SERVER["DOCUMENT_ROOT"];
//        $splits = explode("/", $documentRoot);
//        unset($splits[count($splits)-1]);        
        return static::defaultPath;
    }

}
