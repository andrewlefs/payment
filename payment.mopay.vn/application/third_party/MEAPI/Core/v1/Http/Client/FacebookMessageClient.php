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
use Misc\Http\Receiver;
use Misc\Api;

class FacebookMessageClient extends Client implements ClientInterface {

    /**
     * Maps aliases to Facebook domains.
     *
     * @var array
     */
    public static $DOMAIN_MAP = array(
        'api' => 'https://api.facebook.com/',
        'api_video' => 'https://api-video.facebook.com/',
        'api_read' => 'https://api-read.facebook.com/',
        'graph' => 'https://graph.facebook.com/v2.8/',
        'graph_video' => 'https://graph-video.facebook.com/',
        'www' => 'https://www.facebook.com/',
    );
    protected $receive;
    protected $postType = true;

    public function __construct() {
        $this->setApp("533414373498024");
        $this->setSecret("e48719db47bce8cae1005809cd0ab192");
        $this->setDefaultBaseDomain("facebook.com");
        $this->setDefaultLastLevelDomain("graph");
        $this->getRequestPrototype()->setProtocol("https://");
        $this->setSslVerifypeer(true);
        $this->setCaBundleName("fb_ca_chain_bundle.crt");
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    function getReceive() {
        if ($this->receive == null)
            $this->receive = new Receiver ();
        return $this->receive;
    }

    function setReceive($receive) {
        $this->receive = $receive;
    }

    function setPostType($type = true){
        $this->postType = $type;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request) {
        $request->setPostArray($this->postType);
        return parent::sendRequest($request);
    }

    public function getProfile($sender, $params) {
        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . $sender . json_encode($params));
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());

        if ($result == false || $cached == false) {
            $response = $this->getApi()->call("/" . $sender, "GET", $params);
            //Object response form request by class http Response
            $contents = $response->getContent();
            if (is_array($contents) === true && isset($contents["first_name"])) {
                $this->getController()->getMemcacheObject()->saveMemcache($keyId, $contents, $this->getEndPoint(), 1 * 3600);
                return $contents;
            } else {
                throw new \Exception(
                'Get server list failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            }
        }
        return $result;
    }

    public function sendMessage($access_token, $params) {                
        $response = $this->getApi()->call("/me/messages?access_token=" . $access_token, "POST", $params);
        //Object response form request by class http Response
        $contents = $response->getContent();
        return $contents;
    }

     public function sendTheadSettingsMessage($access_token, $params) {                
        $response = $this->getApi()->call("/me/thread_settings?access_token=" . $access_token, "POST", $params);
        //Object response form request by class http Response
        $contents = $response->getContent();
        return $contents;
    }
    /**
     * Build the URL for given domain alias, path and parameters.
     *
     * @param string $name   The name of the domain
     * @param string $path   Optional path (without a leading slash)
     * @param array  $params Optional query parameters
     *
     * @return string The URL for the given parameters
     */
    protected function buildUrl($name, $path = '', $params = array()) {
        $url = self::$DOMAIN_MAP[$name];
        if ($path) {
            if ($path[0] === '/') {
                $path = substr($path, 1);
            }
            $url .= $path;
        }
        if ($params) {
            $url .= '?' . http_build_query($params, null, '&');
        }

        return $url;
    }

}
