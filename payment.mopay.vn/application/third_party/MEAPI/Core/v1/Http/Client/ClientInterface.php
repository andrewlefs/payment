<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Http\Client;

use Misc\Http\RequestInterface;
use Misc\Http\ResponseInterface;
use Misc\Http\Headers;

interface ClientInterface {

    public function getRequestPrototype();

    /**
     * @param RequestInterface $prototype
     */
    public function setRequestPrototype(RequestInterface $prototype);

    /**
     * @return RequestInterface
     */
    public function createRequest();

    /**
     * @return ResponseInterface
     */
    public function getResponsePrototype();

//    /**
//     * @param ResponseInterface $prototype
//     */
    public function setResponsePrototype(ResponseInterface $prototype);

    /**
     * 
     */
    public function getSslVerifypeer();

    /**
     * 
     * @param boolean $sslVerifypeer
     */
    public function setSslVerifypeer($sslVerifypeer = false);

    /**
     * @return ResponseInterface
     */
    public function createResponse();

    /**
     * @return Headers
     */
    public function getDefaultRequestHeaderds();

    /**
     * @param Headers $headers
     */
    public function setDefaultRequestHeaders(Headers $headers);

    /**
     * @return string
     */
    public function getDefaultBaseDomain();

    /**
     * @return string
     */
    public function getDefaultLastLevelDomain();

    /**
     * @param string $domain
     */
    public function setDefaultBaseDomain($domain);

    /**
     * @param string $domain
     */
    public function setDefaultLastLevelDomain($last_level);

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request);

    public function getEndPoint();
}
