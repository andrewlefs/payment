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
use Misc\Http\Client\Client;

class GApiClient extends Client implements ClientInterface {

    const APP = "game";
    const SECRET = "IDpCJtb6Go10vKGRy5DQ";
    const SERVICE_ID = 0;

    private $serviceId;

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("gapi");
        $this->setSslVerifypeer(false);
        $this->setApp(self::APP);
        $this->setSecret(self::SECRET);
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function getApp() {
        return $this->app == null ? self::APP : $this->app;
    }

    public function getSecret() {
        return $this->secret == null ? self::SECRET : $this->secret;
    }

    public function getServiceId() {
        return $this->serviceId == null ? self::SERVICE_ID : $this->serviceId;
    }

    public function setServiceId($serviceId) {
        $this->serviceId = $serviceId;
    }

    public function getToken(RequestInterface $request) {
        //
        $params = $request->getQueryParams()->getArrayCopy();
        return md5(implode("", $params) . $this->secret);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request) {
        $token = $this->getToken($request);
        $queryParams = $request->getQueryParams();
        $queryParams["app"] = $this->getApp();
        $queryParams["token"] = $token;
        $request->setQueryParams($queryParams);
        //var_dump($this);
        return parent::sendRequest($request);
    }

    /**
     * 
     * @param type $service_name
     * @param type $server_id
     * @param type $cached
     * @return type
     */
    public function getServerInfo($service_name, $server_id, $cached = true) {
        $serverList = $this->getServerList($service_name, $cached);
        if ($serverList !== false) {
            $val = array_search($server_id, array_column($serverList, "server_id"));
            if ($val !== false) {
                $serverInfo = $serverList[$val];
                $serverInfo["server_name"] = trim(preg_replace("/\[\d\]/", "", $serverInfo["server_name"]));
                return $serverInfo;
            }
        }
        return array("server_id" => $server_id, "server_name" => $server_id, "server_id_merge" => $server_id);
    }

    /**
     * 
     * @param type $service_name
     * @param type $cached
     * @return boolean
     * @throws \Exception
     */
    public function getServerList($service_name, $cached = true) {
        $params = args_with_keys(get_defined_vars());

        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode($service_name));
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());

        if ($result == false || $cached == false) {
            $response = $this->getApi()->call(array("control" => "game", "func" => "get_server_list"), "GET", $params);
            //Object response form request by class http Response
            $contents = $response->getContent();
            //var_dump($contents);die;
            if (is_array($contents) === true) {
                if ($contents["code"] === 500102) {
                    $result = $contents["data"]["data"];
                    $this->getController()->getMemcacheObject()->saveMemcache($keyId, $result, $this->getEndPoint(), 1 * 3600);
                } else {
                    return false;
                }
            } else {
                throw new \Exception(
                'Get server list failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            }
        }
        return $result;
    }

    /**
     * 
     * @param array $params
     * 
     * @return array
     */
    public function getPromoItems(array $params) {

        $response = $this->getApi()->call(array("control" => "game", "func" => "get_promo_items"), "GET", $params);
        //Object response form request by class http Response
        $contents = $response->getContent();
        //return false;
        if (is_array($contents) === true) {
            if ($contents["code"] === 500102) {                
                return $contents["data"];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 
     * @param array $params
     * 
     * @return array
     */
    public function getPromoDetailItems(array $params) {

        $response = $this->getApi()->call(array("control" => "game", "func" => "get_promo_detail_items"), "GET", $params);
        //Object response form request by class http Response
        $contents = $response->getContent();
        //return false;
        if (is_array($contents) === true) {
            if ($contents["code"] === 500102) {                
                return $contents["data"];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * 
     * @param type $service_name
     * @param type $mobo_service_id
     * @param type $server_id
     * @return boolean
     * @throws \Exception
     */
    public function getUserInfo($service_name, $mobo_service_id, $server_id) {

        //prepare param body request
        $params = args_with_keys(get_defined_vars());
        $params["time_stamp"] = date('Y-m-d H:i:s', time());

        //send request to server 
        $response = $this->getApi()->call(
                /* path control */
                array("control" => "game", "func" => "get_game_account_info")
                //method
                , "GET"
                //body parameter request
                , $params
        );

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 0) {
                return $contents["data"];
            } else {
                return false;
            }
        } else {
            throw new \Exception(
            'Get user info failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
        }
    }

    /**
     * 
     * @param type $service_name
     * @param type $mobo_service_id
     * @param type $server_id
     * @param array $award
     * @param type $multi
     * @param type $title
     * @param type $content
     * @param type $character_id
     * @return boolean
     * Ngọc phải sử dụng: diamond, và Vàng phải dùng: gold
     */
    public function addItems($service_name, $mobo_service_id, $server_id, array $award, $title = "Title mail send item", $content = "Content mail send item", $character_id = null /* role id */) {

        //prepare param request
        $params = args_with_keys(get_defined_vars());
        $params["time_stamp"] = date('Y-m-d H:i:s', time());
        $params["service_id"] = $this->getServiceId();
        //send request to server
        $response = $this->getApi()
                ->call(
                //path control
                array("control" => "game", "func" => "add_item")
                //method
                , "GET"
                //body parameter request
                , $params);

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 0) {
                return true;
            } else {
                return false;
            }
        } else {
//            throw new \Exception(
//            'Add item failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            $this->getApi()->getLogger()->logFullRequest("add_error", $this->getApi()->getHttpRequest(), $response);
            return false;
        }
    }

    /**
     * 
     * @param type $service_name
     * @param type $mobo_service_id
     * @param type $server_id
     * @param array $award
     * @param type $multi
     * @param type $title
     * @param type $content
     * @param type $character_id
     * @return boolean
     */
    public function minusItems($service_name, $mobo_service_id, $server_id, array $award, $multi = true, $title = "Title mail send item", $content = "Content mail send item", $character_id = null /* role id */) {

        //prepare param request
        $params = args_with_keys(get_defined_vars());
        $params["time_stamp"] = date('Y-m-d H:i:s', time());
        $params["service_id"] = $this->getServiceId();
        //send request to server
        $response = $this->getApi()
                ->call(
                //path control
                array("control" => "game", "func" => "minus_item")
                //method
                , "GET"
                //body parameter request
                , $params);

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 0) {
                return true;
            } else {
                return false;
            }
        } else {
//            throw new \Exception(
//            'Minus item failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            $this->getApi()->getLogger()->logFullRequest("minus_error", $this->getApi()->getHttpRequest(), $response);
            return false;
        }
    }

    /**
     * 
     * @param type $service_name
     * @param type $mobo_service_id
     * @param type $server_id
     * @param array $award
     * @param type $multi
     * @param type $title
     * @param type $content
     * @param type $character_id
     * @return boolean
     * Ngọc phải sử dụng: diamond, và Vàng phải dùng: gold
     */
    public function addMoney($params) {

        $this->setApp("payment");
        $this->setSecret("tPhV6cTNoUtdppNEw9sI");

        //prepare param request
        $params["date"] = time();
        $params["service_id"] = $this->getServiceId();
        $params["service_name"] = $this->getServiceId();
        $params["game_info"] = $params["info"];
        unset($params["info"]);
        //send request to server
        $response = $this->getApi()
                ->call(
                //path control
                array("control" => "payment", "func" => "recharge")
                //method
                , "GET"
                //body parameter request
                , $params);

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 0) {
                return $contents;
            } else {
                return false;
            }
        } else {
//            throw new \Exception(
//            'Add item failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
            $this->getApi()->getLogger()->logFullRequest("add_error", $this->getApi()->getHttpRequest(), $response);
            return false;
        }
    }

}
