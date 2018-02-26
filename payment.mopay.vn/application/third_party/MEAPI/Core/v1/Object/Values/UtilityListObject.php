<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Object\Values;

use Misc\Http\Parameters;

class UtilityListObject extends Parameters {

    static $DEFAULT_LIST = array(
        "all" => array(
            "build" => false,
            "name" => "All Utility tools",
            "icon" => "/v1/utility/images/logo.png",
            "link" => "/",
            "hashtag" => "u",
            "description" => "",
            "term" => "/"
        ),
        "ipnetwork" => array(
            "build" => true,
            "name" => "Network IP Scanner",
            "icon" => "/v1/utility/images/network.webp",
            "link" => "/ipnetwork",
            "hashtag" => "ipnetwork",
            "description" => "",
            "term" => "/"
        ),
        "base64" => array(
            "build" => true,
            "name" => "Base64 Encoder/Decoder",
            "icon" => "/v1/utility/images/base64.webp",
            "link" => "/base64",
            "hashtag" => "base64",
            "description" => "Have to deal with Base64 format? Then this site is made for You! Use the super simple online form below to decode or encode Your data. If You're interested about the inner workings of the Base64 format, just read the detailed description at the bottom of the page",
            "term" => "/"
        ),
        "json" => array(
            "build" => true,
            "name" => "JSON Encoder/Decoder",
            "icon" => "/v1/utility/images/json.webp",
            "link" => "/json",
            "hashtag" => "json",
            "description" => "JSON PARSER ONLINE is a tool which lets you parse json string into a preety and colorful json tree view. JSON Parser Online converts JSON Strings to a friendly readable format.",
            "term" => "/"
        ),
        "md5" => array(
            "build" => true,
            "name" => "MD5 Hash",
            "icon" => "/v1/utility/images/md5.webp",
            "link" => "/md5",
            "hashtag" => "md5",
            "description" => "",
            "term" => "/"
        ),
        "tripledes" => array(
            "build" => true,
            "name" => "TripleDES",
            "icon" => "/v1/utility/images/tripledes.webp",
            "link" => "/tripledes",
            "hashtag" => "tripledes",
            "description" => "",
            "term" => "/"
        ),
        "url" => array(
            "build" => true,
            "name" => "UDEncode (URL encoder/decoder)",
            "icon" => "/v1/utility/images/ude.webp",
            "link" => "/url",
            "hashtag" => "url",
            "description" => "",
            "term" => "/"
        ),
        "qrcode" => array(
            "build" => true,
            "name" => "QR Barcode Scanner",
            "icon" => "/v1/utility/images/qrcode.webp",
            "link" => "/qrcode",
            "hashtag" => "qrcode",
            "description" => "",
            "term" => "/"
        ),
        "social" => array(
            "build" => true,
            "name" => "Social Live Reactions",
            "icon" => "/v1/utility/images/social.webp",
            "link" => "/social",
            "hashtag" => "social",
            "description" => "",
            "term" => "/"
        ),"curl" => array(
            "build" => true,
            "name" => "Curl",
            "icon" => "/v1/utility/images/curl.webp",
            "link" => "/curl",
            "hashtag" => "curl",
            "description" => "",
            "term" => "/"
        )
    );
    private $list;

    public function __construct() {
        
    }

    function getList() {
        if ($this->list == null) {
            $this->list = self::$DEFAULT_LIST;
        }
        return $this->list;
    }

    function setList($list) {
        $this->list = $list;
    }

}
