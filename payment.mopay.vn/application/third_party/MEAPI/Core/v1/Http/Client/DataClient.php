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

class DataClient extends Client implements ClientInterface {

   public function __construct() {
        $this->setDefaultBaseDomain("mobo.vn");
        $this->setDefaultLastLevelDomain("data");        
    }

    public function getEndPoint() {
        return __CLASS__;
    }
   
    public function sendRequest(RequestInterface $request) {
        return parent::sendRequest($request);                
    }
    
    public function getListByCategory($categoryId, $language = 1, $cached = true){
        //prepare param body request        
        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode(array($categoryId, $language)));
        $body = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($body == false || $cached == false) {
            //send request to server
            $response = $this->getApi()->call(
                    /* path control */
                    "/home/get_category/" . $categoryId . "/" .$language
                    //method
                    , "GET"
                    //body parameter request
                    , array()
            );
            //Object response form request by class http Response
            $body = $response->getBody();           
            $this->getController()->getMemcacheObject()->saveMemcache($keyId, $body, $this->getEndPoint(), 24 * 3600);                
        }
        return $body;
    }
    public function getPostContentById($postId, $language = 1, $cached = true){
        //prepare param body request        
        $keyId = $this->getController()->getMemcacheObject()->genCacheId(__CLASS__ . __FUNCTION__ . json_encode(array($postId, $language)));
        $body = $this->getController()->getMemcacheObject()->getMemcache($keyId, $this->getEndPoint());
        if ($body == false || $cached == false) {
            //send request to server
            $response = $this->getApi()->call(
                    /* path control */
                    "/home/get_post_id/" . $postId . "/" .$language
                    //method
                    , "GET"
                    //body parameter request
                    , array()
            );
            //Object response form request by class http Response            
            $body = $response->getBody();           
            
            $this->getController()->getMemcacheObject()->saveMemcache($keyId, $body, $this->getEndPoint(), 24 * 3600);                
        }
        return $body;
    }
}
