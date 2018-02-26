<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Object\Fields;

//require_once APPPATH . 'controllers/grash/autoloader.php';

use Misc\Enum\AbstractEnum;

class GApiFields extends AbstractEnum {

    const APP_SECRET = 'app_secret';
    const PRIVATE_KEY = "private_key";
    const REMOTE_ADDR = 'REMOTE_ADDR';

}
