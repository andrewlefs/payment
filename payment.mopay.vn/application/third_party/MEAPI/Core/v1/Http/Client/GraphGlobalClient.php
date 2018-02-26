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
use Misc\Http\Util;
use Misc\Security;

class GraphGlobalClient extends Client implements ClientInterface {

    protected $responseResult;
    protected $timeSlice;

    public function __construct() {
        $this->setDefaultBaseDomain("dllglobal.net");
        $this->setDefaultLastLevelDomain("graph");
    }

    public function getEndPoint() {
        return __CLASS__;
    }
    public function getTimeSlice() {
        if ($this->timeSlice == null) {
            $this->timeSlice = (int) (time() / 30);
        }
        return $this->timeSlice;
    }

    public function setTimeSlice($timeSlice) {
        $this->timeSlice = $timeSlice;
    }

    public function sendRequest(RequestInterface $request) {
        $header = new Headers();

        $otpCode = Util::getCode($this->getSecret(), $this->getTimeSlice());
        $params = $request->getQueryParams()->getArrayCopy();

        $original = implode("", $params) . $otpCode;
        $token = md5($original . $this->getSecret());

        $header['otp'] = $otpCode;
        $header["app"] = $this->getApp();
        $header["token"] = $token;

        $this->setDefaultRequestHeaders($header);

        $this->responseResult = parent::sendRequest($request);
        //parse result
        return $this->responseResult;
    }

    public function prepareResponse() {
        //var_dump($this->responseResult);
        if ($this->responseResult != NULL) {
            $result = $this->responseResult->getBody();
            $resultDecrypt = Security::decrypt($result, $this->getSecret());

            $endResult = null;
            if (is_array($resultDecrypt))
                $endResult = $resultDecrypt;
            else {
                $endResult = json_decode($result, true);
                if ($endResult == null) {
                    return $this->responseResult->getContent();
                }
            }
            return $endResult;
        } else {
            return null;
        }
    }

}
