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

class MomoClient extends Client implements ClientInterface {

    protected $accessKey;

    public function __construct() {
        $this->setDefaultBaseDomain("momo.vn:18080");
        $this->setDefaultLastLevelDomain("payment");
        $this->getRequestPrototype()->setProtocol("https://");
        $this->setSslVerifypeer(FALSE);
        $this->setTlsvVersion(5);
    }

    public function getAccessKey() {
        return $this->accessKey;
    }

    public function setAccessKey($accessKey) {
        $this->accessKey = $accessKey;
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function sendRequest(RequestInterface $request) {
        $params = $request->getBodyParams()->export();
        $params = array_merge(
                array("partner_code" => $this->getApp(), "access_key" => $this->getAccessKey())
                , $params);
        $data = "";
        foreach ($params as $key => $value) {
            if ($data != "")
                $data .= "&";
            $data .= $key . "=" . $value;
        }
        $signature = hash_hmac("sha256", $data, $this->getSecret());
        $params["signature"] = $signature;

        $request->setBodyParams((new Parameters())->enhance($params));
        $request->setPostArray(2);
        return parent::sendRequest($request);
    }

    public function getPaymentRequest($params) {
        //send request to server        
        $response = $this->getApi()->call("/gw_payment/payment_request", "POST", $params);
        //Object response form request by class http Response            
        return $response->getContent();
    }

}
