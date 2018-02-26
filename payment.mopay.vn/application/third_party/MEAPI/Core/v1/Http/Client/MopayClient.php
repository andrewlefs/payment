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

class MopayClient extends Client implements ClientInterface {

    protected $accessKey;

    public function __construct() {
        $this->setDefaultBaseDomain("mopay.vn");
        $this->setDefaultLastLevelDomain("payment");
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
        $method = $request->getMethod();
        $params = array();
        if ($method === RequestInterface::METHOD_POST && $request->getBodyParams()->count()) {
            $params = array_merge($params, $request->getBodyParams()->export());
        }
        if ($method === RequestInterface::METHOD_GET && $request->getQueryParams()->count()) {
            $params = array_merge($params, $request->getQueryParams()->export());
        }
        $tokenData = implode("", $params);
        $params["app"] = $this->getApp();
        $params["token"] = md5($tokenData . $this->getSecret());
        //var_dump($params);die;
        $request->setQueryParams((new Parameters())->enhance($params));
        return parent::sendRequest($request);
    }

    /**
     * 
     * @param array $params
     * @return Response
     */
    public function getCommitRequest(array $params) {
        //send request to server                
        $response = $this->getApi()->call(array("control" => "adapter", "func" => "verify_momo"), "GET", $params);
        //Object response form request by class http Response            
        return $response;
    }

    /**
     * 
     * @param array $params
     * @return Response
     */
    public function getCommitMpayRequest(array $params) {
        //send request to server                
        $response = $this->getApi()->call(array("control" => "adapter", "func" => "add_money_mpay"), "GET", $params);
        //Object response form request by class http Response            
        return $response;
    }
    
    public function getCommitMpayRequestWebMoney(array $params) {
        //send request to server                
        $response = $this->getApi()->call(array("control" => "adapter", "func" => "add_money_webmoney"), "GET", $params);
        //Object response form request by class http Response            
        return $response;
    }

    /**
     * 
     * @param array $params
     * @return Response
     */
    public function getCommit8595Request(array $params) {
        //send request to server                
        $response = $this->getApi()->call(array("control" => "adapter", "func" => "add_money_8595"), "GET", $params);
        //Object response form request by class http Response            
        return $response;
    }

    /**
     * 
     * @param array $params
     * @return Response
     */
    public function verifyCard(array $params) {
        //send request to server
        $response = $this->getApi()->call(array("control" => "adapter", "func" => "verify_card"), "GET", $params);
        return $response;
    }

    /**
     * 
     * @param array $params
     * @param boolean $cache
     * @return boolean
     * @throws \Exception
     */
    public function getPaymentList(array $params, $cache = true) {

        $params = array_merge($params, array(
            "channel" => "1|me|ref|1.0",
            "platform" => "web",
            "user_agent" => $_SERVER["HTTP_USER_AGENT"],
            "ip" => $_SERVER["REMOTE_ADDR"],
            "lang" => "vi",
            "telco" => "vietname",
            "info" => '{"character_id":"1694489933","character_name":"Laozaza","server_id":"169028"}',
            "version" => '1.0.0'
        ));

        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__);
        $result = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        //send request to server
        if ($result == false || $cached == false) {
            $response = $this->getApi()->call(array("control" => "adapter", "func" => "payment_list"), "GET", $params);

            //Object response form request by class http Response
            $contents = $response->getContent();
            //hardcode bảo trì
            //$resultHardCode = '{"code":110,"desc":"REQUEST_SUCCESS","data":{"title":"","description":"M\u1eddi b\u1ea1n ch\u1ecdn ph\u01b0\u01a1ng th\u1ee9c n\u1ea1p","card":{"title":"Th\u1ebb c\u00e0o \u0111i\u1ec7n tho\u1ea1i","data":[{"type":"card","card":"gate","message":"Gate","description":"Gate","confirm":"B\u1ea1n c\u00f3 mu\u1ed1n n\u1ea1p kh\u00f4ng?","icon":"http:\/\/service.mobo.vn\/assets\/icon\/gate.png","keyboard_state":"full","input":["serial","pin"]},{"type":"card","card":"vms","message":"Mobifone","description":"Mobifone","confirm":"B\u1ea1n c\u00f3 mu\u1ed1n n\u1ea1p kh\u00f4ng?","icon":"http:\/\/service.mobo.vn\/assets\/icon\/vms.png","keyboard_state":"number","input":["serial","pin"]},{"type":"card","card":"vina","message":"Vinaphone","description":"Vinaphone","confirm":"B\u1ea1n c\u00f3 mu\u1ed1n n\u1ea1p kh\u00f4ng?","icon":"http:\/\/service.mobo.vn\/assets\/icon\/vina.png","keyboard_state":"full","input":["serial","pin"]},{"type":"card","card":"viettel","message":"Viettel","description":"Viettel","confirm":"B\u1ea1n c\u00f3 mu\u1ed1n n\u1ea1p kh\u00f4ng?","icon":"http:\/\/service.mobo.vn\/assets\/icon\/viettel.png","keyboard_state":"number","input":["serial","pin"]}]},"banking":{"title":"Ng\u00e2n H\u00e0ng","data":[{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_acb_3x.png","type":"1","code":"acbbank","message":"ACB","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_agribank_3x.png","type":"1","code":"agribank","message":"AgriBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_donga_3x.png","type":"1","code":"dongabank","message":"DongABank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_eximbank_3x.png","type":"1","code":"eximbank","message":"EximBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_maritime_3x.png","type":"1","code":"maritimebank","message":"MaritimeBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_mbbank_3x.png","type":"1","code":"mbbank","message":"MBBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_sacombank_3x.png","type":"1","code":"sacombank","message":"SacomBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_tech_3x.png","type":"1","code":"techcombank","message":"TechcomBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_tienphong_3x.png","type":"1","code":"tienphongbank","message":"TPBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_vietcombank_3x.png","type":"1","code":"vietcombank","message":"VietcomBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_vietinbank_3x.png","type":"1","code":"vietinbank","message":"VietinBank","description":"","interface":"ibanking"},{"icon":"http:\/\/service.mobo.vn\/assets\/icon\/ico_vpbank_3x.png","type":"1","code":"vpbank","message":"VPBank","description":"","interface":"ibanking"}],"prices":[{"message":"100000","description":"100,000 VN\u0110"},{"message":"200000","description":"200,000 VN\u0110"},{"message":"500000","description":"500,000 VN\u0110"},{"message":"1000000","description":"1,000,000 VN\u0110"},{"message":"2000000","description":"2,000,000 VN\u0110"},{"message":"5000000","description":"5,000,000 VN\u0110"},{"message":"10000000","description":"10,000,000 VN\u0110"}]},"hotline":"19006611","app_name":"155","mobo_id":"855304299"},"message":"REQUEST_SUCCESS"}';
            //$contents = json_decode($resultHardCode, true);
//var_dump($contents);die;
            if (is_array($contents) === true) {
                if ($contents["code"] === 110) {
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

    public function getBankRequest(array $params) {
        return $this->getApi()->call(array("control" => "adapter", "func" => "get_link_banking"), "GET", $params);
    }

    /**
     * 
     * @param type $access_token
     * @param type $channel
     * @param type $platform
     * @param type $user_agent
     * @param type $ip
     * @param type $lang
     * @param type $version
     * @return boolean
     * @throws \Exception
     */
    public function getBalance($access_token, $channel, $platform, $user_agent, $ip, $lang, $version) {

        //prepare param body request
        $params = args_with_keys(get_defined_vars());

        //send request to server
        $response = $this->getApi()->call(
                /* path control */
                array("control" => "adapter", "func" => "balance")
                //method
                , "GET"
                //body parameter request
                , $params
        );

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 110) {
                return $contents["data"];
            } else {
                return false;
            }
        } else {
            throw new \Exception(
            'Get user info failed is class ' . get_class($this) . ' position function ' . __FUNCTION__);
        }
    }

}
