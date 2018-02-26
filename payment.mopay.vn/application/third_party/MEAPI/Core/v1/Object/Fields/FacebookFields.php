<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Object\Fields;

//require_once APPPATH . 'controllers/grash/autoloader.php';

use Misc\Enum\AbstractEnum;

class FacebookFields extends AbstractEnum {

    const ACCESS_TOKEN = "_access_token";
    const FANPAGE = "fanpage";
    const SHARE = "share";
    const INVITE = "invite";
    const ACCEPT = "accept";
    const LIKED = "liked";
    const SCOPE = "scope";
    const NONE = "none";
    const REQUEST = "request";
    const LOGIN = "login";

}
