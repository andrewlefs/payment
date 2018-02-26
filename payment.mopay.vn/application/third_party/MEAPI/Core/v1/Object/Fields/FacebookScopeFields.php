<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Object\Fields;

use Misc\Enum\AbstractEnum;

class FacebookScopeFields extends AbstractEnum {

    const EMAIL = "email";
    const PUBLISH_ACTION = "publish_actions";
    const PUBLIC_PROFILE = "public_profile";
    const USER_FRIENDS = "user_friends";

    public static function getStringByScope(array $scope) {
        return implode(",", $scope);
    }

}
