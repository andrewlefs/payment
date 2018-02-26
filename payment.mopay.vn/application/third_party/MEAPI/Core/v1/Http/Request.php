<?php

namespace Misc\Http;

use Misc\Http\Client\ClientInterface;
use Misc\Http\Authorized;

class Request implements RequestInterface {

    /**
     * @var string
     */
    const PROTOCOL_HTTP = 'http://';

    /**
     * @var string
     */
    const PROTOCOL_HTTPS = 'https://';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @var string
     */
    protected $method = self::METHOD_GET;

    /**
     * @var string
     */
    protected $post_array = true;

    /**
     * @var string
     */
    protected $protocol = self::PROTOCOL_HTTP;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $graphVersion;

    /**
     * @var Parameters
     */
    protected $queryParams;

    /**
     * @var Parameters
     */
    protected $bodyParams;

    /**
     * @var Parameters
     */
    protected $fileParams;
    protected $authorized;

    /**
     * @param Client $client
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    public function __clone() {
        $this->queryParams && $this->queryParams = clone $this->queryParams;
        $this->bodyParams && $this->bodyParams = clone $this->bodyParams;
        $this->fileParams && $this->fileParams = clone $this->fileParams;
    }

    /**
     * 
     * @return Authorized
     */
    function getAuthorized() {
        return $this->authorized;
    }

    function setAuthorized(Authorized $authorized) {
        $this->authorized = $authorized;
    }

    /**
     * @return Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol($protocol) {
        $this->protocol = $protocol;
    }

    /**
     * @return string
     */
    public function getDomain() {
        if ($this->domain === null) {
            $subLevel = $this->getClient()->getDefaultLastLevelDomain();
            if (empty($subLevel))
                $this->domain = sprintf(
                        "%s", $this->getClient()->getDefaultBaseDomain());
            else
                $this->domain = sprintf(
                        "%s.%s", $this->getClient()->getDefaultLastLevelDomain(), $this->getClient()->getDefaultBaseDomain());
        }

        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain) {
        $this->domain = $domain;
    }

    /**
     * @param string $last_level_domain
     */
    public function setLastLevelDomain($last_level_domain) {
        $this->domain = sprintf(
                "%s.%s", $last_level_domain, $this->client->getDefaultBaseDomain());
    }

    /**
     * @return Headers
     */
    public function getHeaders() {
        if ($this->headers === null) {
            $this->headers = clone $this->getClient()->getDefaultRequestHeaderds();
        }

        return $this->headers;
    }

    /**
     * @param Headers $headers
     */
    public function setHeaders(Headers $headers) {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getPostArray() {
        return $this->post_array;
    }

    /**
     * @param string $method
     */
    public function setPostArray($method) {
        $this->post_array = $method;
    }

    /**
     * @return string
     */
    public function getPath() {
        if (is_array($this->path)) {
            return "/?" . http_build_query($this->path);
        }
        return $this->path;
    }

    public function getBornPath() {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getGraphVersion() {
        return $this->graphVersion;
    }

    /**
     * @param string $version
     */
    public function setGraphVersion($version) {
        $this->graphVersion = $version;
    }

    /**
     * @return Parameters
     */
    public function getQueryParams() {
        if ($this->queryParams === null) {
            $this->queryParams = new Parameters();
        }

        return $this->queryParams;
    }

    /**
     * @param Parameters $params
     */
    public function setQueryParams(Parameters $params) {
        $this->queryParams = $params;
    }

    protected function getDimiter() {
        if (is_array($this->path))
            return "&";
        else
            return "?";
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->getProtocol() . $this->getDomain()
                . $this->getGraphVersion() . $this->getPath()
                . ($this->getQueryParams()->count() ? $this->getDimiter() : null)
                . http_build_query($this->getQueryParams()->export());
    }

    /**
     * @return Parameters
     */
    public function getBodyParams() {
        if ($this->bodyParams === null) {
            $this->bodyParams = new Parameters();
        }

        return $this->bodyParams;
    }

    /**
     * @param Parameters $params
     */
    public function setBodyParams(Parameters $params) {
        //var_dump($params);die;
        $this->bodyParams = $params;
    }

    /**
     * @return Parameters
     */
    public function getFileParams() {
        if ($this->fileParams === null) {
            $this->fileParams = new Parameters();
        }

        return $this->fileParams;
    }

    /**
     * @param Parameters $params
     */
    public function setFileParams(Parameters $params) {
        $this->fileParams = $params;
    }

    /**
     * @return ResponseInterface
     */
    public function execute() {
        return $this->getClient()->sendRequest($this);
    }

    /**
     * @return Request
     * @see RequestInterface::createClone()
     */
    public function createClone() {
        return clone $this;
    }

}
