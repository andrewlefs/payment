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

class GraphTimeClient extends Client implements ClientInterface {

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("graph");
    }

    public function getEndPoint() {
        return __CLASS__;
    }

}

class GraphClient extends Client implements ClientInterface {

    protected $responseResult;
    protected $timeSlice;
    protected $athwartTimeSlice;

    const APP = "skylight";
    const SECRET = "QEOODZHBTPE6ZJI7";

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("graph");
        $this->setApp(self::APP);
        $this->setSecret(self::SECRET);
        OneTimePassword::reset();
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function getAthwartTimeSlice() {

        $keyId = $this->getController()->getMemcacheObject()->genCacheId($this->getEndPoint() . __FUNCTION__);
        $this->athwartTimeSlice = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());

        if ($this->athwartTimeSlice === false) {
            $api = new Api(new GraphTimeClient());
            $api->getHttpClient()->setApp($this->getApp());
            $api->getHttpClient()->setSecret($this->getSecret());

            $timeServer = $api->call(array("control" => "user", "func" => "ntp"))->getContent();
            //tổ chức cache server nếu cần
            if ($timeServer["code"] === 103) {
                $this->athwartTimeSlice = ((int) ($timeServer["data"]["timestamps"])) - time();
                $this->getController()->getMemcacheObject()->saveMemcache($keyId, $this->athwartTimeSlice, $this->getEndPoint(), 900);
            }
        }
        return $this->athwartTimeSlice;
    }

    function getTimeSlice() {
        if ($this->timeSlice == null) {
            $this->timeSlice = time() + $this->getAthwartTimeSlice();
        }
        return $this->timeSlice;
    }

    public function sendRequest(RequestInterface $request) {

        $paths = $request->getBornPath();
        $params = $request->getQueryParams()->getArrayCopy();
        $params = array_merge_is_null($paths, $params);

        $params['otp'] = OneTimePassword::getInstance()->getCode($this->getSecret(), $this->getTimeSlice());
        $params["app"] = $this->getApp();
        //echo implode("", $params) . $this->getSecret(), "</br>";
        $params["token"] = md5(implode("", $params) . $this->getSecret());
        $request->getQueryParams()->enhance($params);
        $this->responseResult = parent::sendRequest($request);
        //parse result
        return $this->responseResult;
    }

    /**
     * 
     * @param array $params array("access_token" => ?)
     * @return boolean or array account info
     * array feilds  'account_id', 'account', 'email', 'channel', 'device_id'
     */
    public function verifyAccessToken(array $params = array()) {
        $result = $this->getApi()->call(array("control" => "user", "func" => "verify_access_token"), "GET", $params)->getContent();
        if ($result["code"] === 500040) {
            return $result["data"];
        } else {
            return false;
        }
    }

    /**
     * 
     * @param array $params
     * @return boolean
     */
    public function requestAccessToken(array $params = array()) {
        $result = $this->getApi()->call(array("control" => "user", "func" => "request_access_token")
                        , "GET", $params)->getContent();

        if ($result["code"] === 500040) {
            return $result["data"];
        } else {
            return false;
        }
    }

    public function getMoboAccount($mobo, $service_id, $cached = true) {
        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode(array($mobo, $service_id)));
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($result == false || $cached == false) {
            $data = $this->getMoboListAccount($mobo, false);
            $result = isset($data[$service_id]) ? $data[$service_id] : null;
            $this->getController()->getMemcacheObject()->saveMemcache($keyId, $result, $this->getEndPoint(), 24 * 3600);
        }        
        return $result;
    }

    public function getMoboListAccount($mobo, $cached = true) {

        //prepare param body request
        $params = args_with_keys(get_defined_vars());

        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode(array($mobo, $service_id)));
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($result == false || $cached == false) {
            //send request to server
            $response = $this->getApi()->call(
                    /* path control */
                    array("control" => "inside", "func" => "search_graph")
                    //method
                    , "GET"
                    //body parameter request
                    , $params
            );
            //Object response form request by class http Response
            $contents = $response->getContent();
            //var_dump($contents);die;
            if (is_array($contents) === true) {
                if ($contents["code"] === 900000) {
                    $result = $contents["data"];
                    $this->getController()->getMemcacheObject()->saveMemcache($keyId, $result, $this->getEndPoint(), 24 * 3600);
                } else {
                    return false;
                }
            } else {
                throw new \Exception(
                'Get user info failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            }
        }
        return $result;
    }

    public function getListAppInfo() {
        //prepare param body request        
        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode(array()));
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($result == false || $cached == false) {
            //send request to server
            $response = $this->getApi()->call(
                    /* path control */
                    array("control" => "user", "func" => "get_app")
                    //method
                    , "GET"
                    //body parameter request
                    , array("partner" => "off")
            );
            //Object response form request by class http Response
            $contents = $response->getContent();
            if (is_array($contents) === true) {
                if ($contents["code"] === 500102) {
                    $result = $contents["data"];
                    $this->getController()->getMemcacheObject()->saveMemcache($keyId, $result, $this->getEndPoint(), 24 * 3600);
                } else {
                    return false;
                }
            } else {
                throw new \Exception(
                'Get user info failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            }
        }
        return $result;
    }

    public function getAppInfo($app) {
        //prepare param body request        
        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode(array($app)));
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($result == false || $cached == false) {
            //send request to server
            $results = $this->getListAppInfo();
            if ($results == true) {
                foreach ($results as $key => $value) {
                    if ($value["app"] == $app) {
                        $result = $value;
                        break;
                    }
                }
                $this->getController()->getMemcacheObject()->saveMemcache($keyId, $result, $this->getEndPoint(), 24 * 3600);
            } else {
                return false;
            }
        }
        return $result;
    }

}
