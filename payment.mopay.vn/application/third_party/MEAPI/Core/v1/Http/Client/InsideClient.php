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
use Misc\Http\OneTimePassword;
use Misc\Http\Authorized;

interface InsideClientInterface {

    public function getCardList($mobo_id);
}

class InsideClient extends Client implements ClientInterface {

    protected $responseResult;
    protected $timeSlice;

    const APP = "skylight";
    const SECRET = "QEOODZHBTPE6ZJI7";

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("inside");

        $this->setApp(self::APP);
        $this->setSecret(self::SECRET);
        OneTimePassword::reset();
    }

    public function getEndPoint() {
        return __CLASS__;
    }

    public function getTimeSlice() {
        if ($this->timeSlice == null) {
            $this->timeSlice = time();
        }
        return $this->timeSlice;
    }

    public function setTimeSlice($timeSlice) {
        $this->timeSlice = $timeSlice;
    }

    public function sendRequest(RequestInterface $request) {

        $params = $request->getQueryParams()->getArrayCopy();
        $paths = args_with_not_empty_keys($params, array("control", "func"));
        unset($params["control"], $params["func"]);

        $params['otp'] = OneTimePassword::getInstance()->getCode($this->getSecret(), $this->getTimeSlice());
        $params["token"] = md5(implode("", $params) . $this->getSecret());

        $params = array_merge_is_null($paths, $params);
        $params["app"] = $this->getApp();
        $request->setAuthorized(new Authorized("ttkt", "30122014@!@#"));

        $request->getQueryParams()->enhance($params);
        $this->responseResult = parent::sendRequest($request);
        //parse result
        return $this->responseResult;
    }

    public function callUseCampaginItem($service_id, $campaign_id, $id, $server_id, $character_id, $character_name) {

        //prepare param body request
        $params = args_with_keys(get_defined_vars());
        $params = array_merge(array("control" => "api_game_mopay", "func" => "campaign_mopay_add_money_game"), $params);
        //send request to server
        $response = $this->getApi()->call("/service", "GET", $params);
        //Object response form request by class http Response
        $contents = $response->getContent();

        if (is_array($contents) === true) {
            if ($contents["code"] === 0) {
                return $contents;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getSandboxCampaginItemList($mobo_id, $service_id, $server_id) {

        //prepare param body request
        $params = args_with_keys(get_defined_vars());
        $params = array_merge(array("control" => "api_game_mopay", "func" => "campaign_mopay_go_game"), $params);

        //send request to server
        $response = $this->getApi()->call("/service", "GET", $params);

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 0) {

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
     * @param type $app_name
     * @param type $cached
     * @return type String
     */
    public function getSecretkey($service_id, $cached = true) {
        $getApp = $this->getSecretList($cached);
        //var_dump($getApp);
        if ($getApp !== false) {
            
            $val = array_search($service_id, array_column($getApp, "service_id"));
            if ($val !== false) {
                return $getApp[$val];
            }
        }
        return null;
    }

    /**
     *
     * @param type $app_name
     * @param type $cached
     * @return type String
     */
    public function getSecretkeyByAppId($service_id, $cached = true) {
        $getApp = $this->getSecretList($cached);
        if ($getApp !== false) {
            $val = array_search($service_id, array_column($getApp, "app"));
            if ($val !== false) {
                return $getApp[$val];
            }
        }
        return null;
    }

    /**
     *
     * @param type $cached
     * @return \ArrayObject
     * @throws \Exception
     */
    public function getSecretList($cached = true) {

        $params = args_with_keys(get_defined_vars());
        $params = array_merge(array("control" => "api_game_mopay", "func" => "list_app"), $params);

        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__);
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());


//        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
//        
//        foreach ($result as $key => $value){
//            echo "INSERT INTO `game` (`game_id`, `name`, `is_active`, `secret_key`, `amount_not_resolved`) VALUES('" , $value["app"], "','", $value["description"], "',1,'", md5($value["private_key"]), "', 0);<br>";
//        }
//        die;
        if ($result == false || $cached == false) {

            $response = $this->getApi()->call("/service", "GET", $params);

            //Object response form request by class http Response
            $contents = $response->getContent();

            //var_dump($contents);die;
            if (is_array($contents) === true) {
                if ($contents["code"] === 0) {
                    $decyptResult = Security::decrypt($contents["data"], $this->getSecret());
                    $result = $decyptResult["data"];
                    //var_dump($result);die;
                    $this->getController()->getMemcacheObject()->saveMemcache($keyId, $result, $this->getEndPoint(), 1 * 3600);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        //var_dump($result);die;
        return $result;
    }

}
