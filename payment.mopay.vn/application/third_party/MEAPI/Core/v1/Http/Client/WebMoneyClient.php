<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Http\Client;

use Misc\Http\Client\ClientCurl;
use Misc\Http\RequestInterface;
use Misc\Http\Request;
use Misc\Http\ResponseInterface;
use Misc\Http\Headers;
use Misc\Http\Client\ClientInterface;
use Misc\Http\Parameters;
use Misc\Http\Adapter\CurlAdapter;
use Misc\Http\Response;
use Misc\Http\Exception\EmptyResponseException;
use Misc\Http\Exception\RequestException;
use Misc\Security;
use Misc\Api;
use Misc\Http\OneTimePassword;

class WebMoneyClient extends Client implements ClientInterface {

    //WebMoney
    const WM_Merchant_Code = "WMTEST";
    const WM_Passcode = "M_HASH";
    const WM_Secret_Key = "test";
    const WMMERCHANT_HOST = 'https://apimerchant.webmoney.com.vn';
    const WMMODE = "sandbox";

    public function __construct() {        
        $this->getRequestPrototype()->setProtocol("https://");
        $this->setSslVerifypeer(FALSE);
        $this->setTlsvVersion(5);
    }

    public function get_Merchant_Code() {
        return $this->WM_Merchant_Code;
    }
    
    public function get_Passcode() {
        return $this->WM_Passcode;
    }
    
    public function get_Secret_Key() {
        return $this->WM_Secret_Key;
    }  

    public function getEndPoint() {
        return __CLASS__;
    }

  

}
