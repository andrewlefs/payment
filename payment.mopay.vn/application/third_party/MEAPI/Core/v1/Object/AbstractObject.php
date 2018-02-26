<?php

namespace Misc\Object;

use Misc\Enum\EmptyEnum;
use Misc\Object\Values\ResultObject;

abstract class AbstractObject {

    protected $resultObject;

    /**
     * @var mixed[] set of key value pairs representing data
     */
    protected $data = array();

    public function __construct() {
        $this->data = static::getFieldsEnum()->getValuesMap();
    }

    /**
     * 
     * @return ResultObject
     */
    public function getReturnResult() {
        if ($this->resultObject == null) {
            $this->resultObject = new ResultObject();
        }
        return $this->resultObject;
    }

    /**
     * 
     * @param ResultObject $result
     */
    public function setResult(ResultObject $result) {
        if ($this->resultObject == null) {
            $this->resultObject = new ResultObject();
        }
        $this->resultObject = $result;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            throw new \InvalidArgumentException(
            $name . ' is not a field of ' . get_class($this));
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return array_key_exists($name, $this->data);
    }
    
    /**
     * @param array
     * @return $this
     */
    public function setData(array $data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        
        return $this;
    }

    /**
     * Like setData but will skip field validation
     *
     * @param array
     * @return $this
     */
    public function setDataWithoutValidation(array $data) {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function exportValue($value) {
        switch (true) {
            case $value === null:
                break;
            case $value instanceof AbstractObject:
                $value = $value->exportData();
                break;
            case is_array($value):
                foreach ($value as $key => $sub_value) {
                    if ($sub_value === null) {
                        unset($value[$key]);
                    } else {
                        $value[$key] = $this->exportValue($sub_value);
                    }
                }
                break;
        }
        return $value;
    }

    /**
     * @return array
     */
    public function exportData() {
        return $this->exportValue($this->data);
    }

    /**
     * @return EmptyEnum
     */
    public static function getFieldsEnum() {
        return EmptyEnum::getInstance();
    }

    /**
     * @return array
     */
    public static function getFields() {
        return static::getFieldsEnum()->getValues();
    }

    /**
     * @return string
     */
    public static function className() {
        return get_called_class();
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
