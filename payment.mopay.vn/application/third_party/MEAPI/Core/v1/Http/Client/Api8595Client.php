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
use Misc\Http\Util;
use Misc\Crypt\RSA;

class Api8595Client extends Client implements ClientInterface {

    protected $connection = 2;
    protected $publicKeyPair = "-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBALi+3dNA/1SJ0NEEhBMy5eW+JoujHWoA
rUZBq18YLX9qhfzpk+niJ9tGZNKPaU4ICY/RZ7tiK+njxz0N8Mp2Oq0CAwEAAQ==
-----END PUBLIC KEY-----";
    protected $privateKeyPair = "-----BEGIN RSA PRIVATE KEY-----
MIIBOgIBAAJBALi+3dNA/1SJ0NEEhBMy5eW+JoujHWoArUZBq18YLX9qhfzpk+ni
J9tGZNKPaU4ICY/RZ7tiK+njxz0N8Mp2Oq0CAwEAAQJAI8C0VyzN/QNKyRiRotGH
0kVRWlav25LT9bPBwX6BV5XmL3FimPczQbAGoARSzjADg9QPd7WTkJWqelk5WVSx
wQIhAPOKRsTuLz8ShXOyAcMAtZStwCypaXz2cTXJd8C5nYGxAiEAwjKJ5mkK2UO7
bNt2ezz57vEiLgP2ZaM6zBEnI4Ug670CIC/6asy7G8WyMWZEiEJRbnRW8ogZ6/U5
W7477YKjTptRAiBze9Rv1dWiwFmr8ZKy1N8YjgMydB7J8Fjd0/F/eQLLzQIhAMWa
UVZp7DNwEkvYwKESO48xKJDHNxL7TK2xzWcHKDeH
-----END RSA PRIVATE KEY-----";
    protected $authorize = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjb25uZWN0aW9uSWQiOiI1OTA5ODVjN2U1MzI1YmY1Y2YzNWE1OTAiLCJpYXQiOjE0OTM4MTY3NDV9.vqW4jhfQGyuie4rcTEDaf5WMP0SOLkvICQ4B7ju-BWQ";

    public function __construct() {
        $this->setDefaultBaseDomain("app1.vn");
        $this->setDefaultLastLevelDomain("8595");
    }

    public function getPrivateKey() {
        return $this->privateKeyPair;
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function sendRequest(RequestInterface $request) {
        $headers = new Headers();
        $headers["Authorization"] = $this->authorize;
        $request->setHeaders($headers);
        return parent::sendRequest($request);
    }

    /**
     * 
     * @param array $params
     * @return Response
     */
    public function sendCardRequest(array $params) {
        //send request to server                
        $response = $this->getApi()->call("/ScratchCard", "POST", $params);
        return $response;
    }

}
