<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc;

use Misc\Object\ModelObject;
use Misc\Http\Receiver;
use Misc\Object\Fields\HeaderField;
use Misc\Object\Values\ResultObject;

class Controller extends \CI_Controller {

    /**
     * @var Api
     */
    protected static $instance;

    /**
     *
     * @var Receiver
     */
    protected $receiver;

    /**
     *
     * @var string
     */
    protected $appId;

    /**
     *
     * @var integer
     */
    protected $dbConfig;

    /**
     *
     * @var string
     */
    protected $pathRoot;

    /**
     *
     * @var array $data
     */
    protected $data;

    /**
     *
     * @var Memcached
     */
    protected $memcached;

    /**
     * Độ lệch time server
     * @var int
     */
    protected $athwartTimeSlice;


    public function __construct() {
        parent::__construct();
        static::setInstance($this);
        //$this->bindingLanguage();
    }

    private $state;

    /**
     *
     * @return Receiver
     */
    public function getReceiver() {
        if ($this->receiver == null)
            $this->receiver = new Receiver();
        return $this->receiver;
    }


    public function _location($url) {
        header("location: " . $url);
        die;
    }

    /**
     *
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver) {
        $this->receiver = $receiver;
    }

    /**
     *
     * @return integer value equals 1002
     */
    public function getAppId() {
        if ($this->appId == null) {
            $paramHeaders = $this->getReceiver()->getHeaders();
            if (isset($paramHeaders[HeaderField::APP])) {
                $this->appId = $paramHeaders[HeaderField::APP];
            } else {
                $paramBodys = $this->getReceiver()->getBodys();
                if (isset($paramBodys[HeaderField::APP]))
                    $this->appId = $paramBodys[HeaderField::APP];
            }
        }
        return $this->appId;
    }

    protected function genCacheId($keyId) {
        return md5($this->getAppId() . $keyId);
    }

    /**
     *
     * @return string secret key by app id 1002
     */
    public function getSecret($app = -1) {
        if ($app == -1) {
            $getsecretkey = $this->getInsideClient()->getSecretkey($this->getAppId(), true);
            if ($getsecretkey !== FALSE) {
                return $getsecretkey[GApiFields::PRIVATE_KEY];
            }
        } else {
            $getsecretkey = $this->getInsideClient()->getSecretkey($app, true);
            if ($getsecretkey !== FALSE) {
                return $getsecretkey[GApiFields::PRIVATE_KEY];
            }
        }
    }

    /**
     *
     * @return string
     */
    public function getPathRoot() {
        return $this->pathRoot;
    }

    /**
     * set path view
     * default will view path
     * @param string $pathRoot
     */
    function setPathRoot($pathRoot = "") {
        $this->pathRoot = $pathRoot;
    }

    public function getPathView() {
        //APPPATH . 'views/' . $base_public . 
        return APPPATH . 'views/' . $this->getPathRoot();
    }

    /**
     *
     * @return array
     */
    function getData() {
        return $this->data;
    }

    /**
     *
     * @param mixed $message
     */
    public function setMessage($message) {
        $this->data["message"] = $message;
    }

    /**
     * Add new key data to this data of class
     * @param mixed $key
     * @param mixed $data
     */
    public function addData($key, $data) {
        $this->data[$key] = $data;
    }

    /**
     * Genaral data from this constants value and properties by class
     *
     * @return array
     */
    function getThisData() {
        $values = array();

        $oClass = new \ReflectionClass(__CLASS__);
        $oContants = $oClass->getConstants();

        foreach ($oContants as $key => $value) {
            $values[$key] = $value;
        }

        $oProperties = $oClass->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $thisMethods = get_class_methods(__CLASS__);
        foreach ($oProperties as $key => $value) {
            try {
                $propertise = json_decode(json_encode($value), true);
                $values[$propertise["name"]] = $this->{$propertise["name"]};
            } catch (\Exception $ex) {
                //var_dump($ex);
                continue;
            }
        }
        return $values;
    }

    function setData($data) {
        $this->data = $data;
    }

    /**
     * Like setData but will skip field validation
     *
     * @param array
     * @return $this
     */
    public function setDataWithoutValidation(array $data) {
        if ($data == false)
            return $this;
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }
    public function prepareArray($data) {
        if (is_object($data)) {
            return $data;
        } elseif (is_array($data)) {
            $reBuilds = array();
            foreach ($data as $key => $value) {
                $reBuilds[$key] = $this->prepareArray($value);
            }
            return $reBuilds;
        } else if (is_json($data)) {
            $jsonArrays = json_decode($data, true);
            if (is_array($jsonArrays)) {
                $reBuilds = array();
                foreach ($jsonArrays as $key => $value) {
                    $reBuilds[$key] = $this->prepareArray($value);
                }
                return $reBuilds;
            } else {
                return $jsonArrays;
            }
        } else if (is_scalar($data)) {
            return $data;
        } else {
            return $data;
        }
    }

    /**
     * @return Controller|null
     */
    public static function instance() {
        return static::$instance;
    }

    /**
     * @param Api $instance
     */
    public static function setInstance(Controller $instance) {
        static::$instance = $instance;
    }

}

?>
