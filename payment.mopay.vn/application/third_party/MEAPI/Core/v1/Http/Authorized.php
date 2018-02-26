<?php

namespace Misc\Http;

use Misc\Http\ReceiverInterface;

class Authorized {

    private $username;
    private $password;

    public function __construct($username = "", $password = "") {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getString() {
        return sprintf("%s:%s", $this->getUsername(), $this->getPassword());
    }

}
