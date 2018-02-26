<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Http\Client;

use Misc\Controller;
use Misc\Api;
use Misc\Http\Client\ClientCurl;
use Misc\Http\RequestInterface;
use Misc\Http\Request;
use Misc\Http\ResponseInterface;
use Misc\Http\Headers;
use Misc\Http\Parameters;
use Misc\Http\Adapter\CurlAdapter;
use Misc\Http\Response;
use Misc\Http\Exception\EmptyResponseException;
use Misc\Http\Exception\RequestException;
use Misc\Http\Client\ClientInterface;

abstract class Client implements ClientInterface {

//    private $api_url_payment = 'http://gapi.mobo.vn/';
//    private $api_url_data = 'http://gapi.mobo.vn/';
    protected $app = 'graph.dxglobal.net';
    protected $secret = 'YAtSTMfEAP';

    /**
     * A CSRF state variable to assist in the defense against CSRF attacks.
     *
     * @var string
     */
    protected $state;

    const VERSION = "2.6";

    /**
     * @var string
     */
    const DEFAULT_GRAPH_BASE_DOMAIN = 'dllglobal.net';

    /**
     * @var string
     */
    protected $defaultLastLevelDomain = '';

    /**
     * @var RequestInterface
     */
    protected $requestPrototype;

    /**
     * @var ResponseInterface
     */
    protected $responsePrototype;

    /**
     * @var Headers
     */
    protected $defaultRequestHeaders;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     *
     * @var type string
     */
    protected $caBundleName;

    /**
     * @var string
     */
    protected $caBundlePath;

    /**
     * @var string
     */
    protected $defaultBaseDomain = self::DEFAULT_GRAPH_BASE_DOMAIN;

    /**
     *
     * @var type boolean
     */
    protected $sslVerifypeer = false;
    protected $tlsvVersion = -1;

    /**
     *
     * @var Controller
     */
    protected $controller;

    /**
     *
     * @var Api
     */
    protected $api;

    /**
     * @return RequestInterface
     */
    public function getRequestPrototype() {
        if ($this->requestPrototype === null) {
            $this->requestPrototype = new Request($this);
        }

        return $this->requestPrototype;
    }

    /**
     * @param RequestInterface $prototype
     */
    public function setRequestPrototype(RequestInterface $prototype) {
        $this->requestPrototype = $prototype;
    }

    public function getController() {
        if ($this->controller == null) {
            $this->controller = new Controller();
        }
        return $this->controller;
    }

    public function setController(Controller $controller) {
        $this->controller = $controller;
    }

    public function getTlsvVersion() {
        return $this->tlsvVersion;
    }

    public function setTlsvVersion($tlsvVersion) {
        $this->tlsvVersion = $tlsvVersion;
    }

    /**
     *
     * @return Api
     */
    public function getApi() {
        if ($this->api == null) {
            $this->api = new Api($this);
            $this->api->getHttpClient()->setApp($this->getApp());
            $this->api->getHttpClient()->setSecret($this->getSecret());
        }
        //$this->getTimeSlice();
        return $this->api;
    }

    /**
     * 
     * @return type string
     */
    function getSslVerifypeer() {
        return $this->sslVerifypeer;
    }

    /**
     * 
     * @param boolean $sslVerifypeer
     */
    function setSslVerifypeer($sslVerifypeer = false) {
        $this->sslVerifypeer = $sslVerifypeer;
    }

    /**
     * @return RequestInterface
     */
    public function createRequest() {
        return $this->getRequestPrototype()->createClone();
    }

    /**
     * @return ResponseInterface
     */
    public function getResponsePrototype() {
        if ($this->responsePrototype === null) {
            $this->responsePrototype = new Response();
        }

        return $this->responsePrototype;
    }

    public function setApp($app) {
        $this->app = $app;
    }

    public function getApp() {
        return $this->app;
    }

    public function setSecret($secret) {
        $this->secret = $secret;
    }

    public function getSecret() {
        return $this->secret;
    }

    /**
     * @param ResponseInterface $prototype
     */
    public function setResponsePrototype(ResponseInterface $prototype) {
        $this->responsePrototype = $prototype;
    }

    /**
     * @return ResponseInterface
     */
    public function createResponse() {
        return clone $this->getResponsePrototype();
    }

    /**
     * @return Headers
     */
    public function getDefaultRequestHeaderds() {
        if ($this->defaultRequestHeaders === null) {
            $this->defaultRequestHeaders = new Headers(array(
            ));
        }

        return $this->defaultRequestHeaders;
    }

    /**
     * @param Headers $headers
     */
    public function setDefaultRequestHeaders(Headers $headers) {
        $this->defaultRequestHeaders = $headers;
    }

    /**
     * @return string
     */
    public function getDefaultBaseDomain() {
        return $this->defaultBaseDomain;
    }

    /**
     * @return string
     */
    public function getDefaultLastLevelDomain() {
        return $this->defaultLastLevelDomain;
    }

    /**
     * @param string $domain
     */
    public function setDefaultLastLevelDomain($lsst_domain) {
        $this->defaultLastLevelDomain = $lsst_domain;
    }

    /**
     * @param string $domain
     */
    public function setDefaultBaseDomain($domain) {
        $this->defaultBaseDomain = $domain;
    }

    /**
     * @return CurlAdapter
     */
    public function getAdapter() {
        if ($this->adapter === null) {
            $this->adapter = new CurlAdapter($this);
        }

        return $this->adapter;
    }

    /**
     * 
     * @return type string
     */
    public function getCaBundleName() {
        return $this->caBundleName;
    }

    /**
     * 
     * @param string $caBundleName
     */
    public function setCaBundleName($caBundleName) {
        $this->caBundleName = $caBundleName;
    }

    /**
     * @return string
     */
    public function getCaBundlePath() {
        if ($this->getSslVerifypeer() === false)
            return false;
        if ($this->caBundlePath === null) {
            $this->caBundlePath = __DIR__ . DIRECTORY_SEPARATOR
                    . $this->getCaBundleName();
        }

        return $this->caBundlePath;
    }

    /**
     * @param string $path
     */
    public function setCaBundlePath($path) {
        $this->caBundlePath = $path;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request) {
        //var_dump($request);die;
        $response = $this->getAdapter()->sendRequest($request);
        $response->setRequest($request);
        $response_content = $response->getContent();

        if ($response_content === null) {
            //throw new EmptyResponseException($response->getStatusCode());
        }

        if (is_array($response_content) && array_key_exists('error', $response_content)) {

            throw RequestException::create(
                    $response->getContent(), $response->getStatusCode());
        }
        //xử lý data tại bước này


        return $response;
    }

    /**
     * Lays down a CSRF state token for this process.
     *
     * @return void
     */
    public function establishCSRFTokenState() {
        if ($this->state === null) {
            $this->state = md5(uniqid(mt_rand(), true));
        }
    }

}
