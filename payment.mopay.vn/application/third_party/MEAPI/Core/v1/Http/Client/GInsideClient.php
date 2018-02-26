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

interface GInsideClientInterface {

}

class GInsideClient extends Client implements ClientInterface {

    protected $responseResult;
    protected $timeSlice;

    const APP = "skylight";
    const SECRET = "7fe109s62d15c61g1f937deae1dc3d";

    public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("ginside");

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

        $params["token"] = md5(implode("", $params) . $this->getSecret());

        $params = array_merge_is_null($paths, $params);

        $request->getQueryParams()->enhance($params);
        $this->responseResult = parent::sendRequest($request);
        //parse result
        return $this->responseResult;
    }


    public function getGiftCode($params = array()) {

        //prepare param body request
        //$params = args_with_keys(get_defined_vars());
        $params = array_merge(array("control" => "giftcodemanager", "func" => "checkgiftcode"), $params);

        //send request to server
        $response = $this->getApi()->call("/", "GET", $params);

        //Object response form request by class http Response
        $contents = $response->getContent();
        if (is_array($contents) === true) {
            if ($contents["code"] === 1000) {

                return $contents["data"];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



}
