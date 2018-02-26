<?php

namespace Misc\Http\Exception;

use Misc\Exception\Exception;

class RequestException extends Exception {

    /**
     * @var int Status code for the response causing the exception
     */
    protected $statusCode;

    /**
     * @var int|null
     */
    protected $errorCode;

    /**
     * @var int|null
     */
    protected $errorSubcode;

    /**
     * @var string|null
     */
    protected $errorMessage;

    /**
     * @var string|null
     */
    protected $errorUserTitle;

    /**
     * @var string|null
     */
    protected $errorUserMessage;

    /**
     * @var int|null
     */
    protected $errorType;

    /**
     * @var array|null
     */
    protected $errorBlameFieldSpecs;

    /**
     * @param array $response_data The response from the Graph API
     * @param int $status_code
     */
    public function __construct(
    array $response_data, $status_code) {

        $this->statusCode = $status_code;
        $error_data = static::getErrorData($response_data);
        //var_dump($error_data);die;
        parent::__construct($error_data['message'], $error_data['code']);

        $this->errorSubcode = $error_data['error_subcode'];
        $this->errorUserTitle = $error_data['error_user_title'];
        $this->errorUserMessage = $error_data['error_user_msg'];
        $this->errorBlameFieldSpecs = $error_data['error_blame_field_specs'];
    }

    /**
     * @param array $array
     * @param string|int $key
     * @param mixed $default
     * @return mixed
     */
    protected static function idx(array $array, $key, $default = null) {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * @param array $response_data
     * @return array
     */
    protected static function getErrorData(array $response_data) {
        $error_data = static::idx($response_data, 'error', array());

        return array(
            'code' => static::idx($response_data, 'code'),
            'error_subcode' => static::idx($response_data, 'error_subcode'),
            'error' => static::idx($response_data, 'error'),
            'error_description' => static::idx($response_data, 'error_description'),
            'message' => static::idx($response_data, 'message'),
            'error_user_title' => static::idx($response_data, 'error_user_title'),
            'error_user_msg' => static::idx($response_data, 'error_user_msg'),
            //'error_blame_field_specs' =>            static::idx(static::idx($response_data, 'error_data', array()), 'blame_field_specs'),
            'type' => static::idx($response_data, 'type'),
        );
    }

    /**
     * Process an error payload from the Graph API and return the appropriate
     * exception subclass.
     * @param array $response_data the decoded response from the Graph API
     * @param int $status_code the HTTP response code
     * @return RequestException
     */
    public static function create(array $response_data, $status_code) {        
        $error_data = static::getErrorData($response_data);       
        if (in_array($error_data['error_subcode'], array(458, 459, 460, 463, 464, 467)) || in_array($error_data['code'], array(100, 102, 190)) || $error_data['type'] === 'OAuthException') {
            return new AuthorizationException($response_data, $status_code);
        } elseif (in_array($error_data['code'], array(1, 2))) {
            return new ServerException($response_data, $status_code);
        } elseif (in_array($error_data['code'], array(4, 17, 341))) {
            return new ThrottleException($response_data, $status_code);
        } elseif ($error_data['code'] == 506) {
            return new ClientException($response_data, $status_code);
        } elseif ($error_data['code'] == 10 || ($error_data['code'] >= 200 && $error_data['code'] <= 299)) {
            return new PermissionException($response_data, $status_code);
        } else {
            return new self($response_data, $status_code);
        }
    }

    /**
     * @return int
     */
    public function getHttpStatusCode() {
        return $this->statusCode;
    }

    /**
     * @return int|null
     */
    public function getErrorSubcode() {
        return $this->errorSubcode;
    }

    /**
     * @return string|null
     */
    public function getErrorUserTitle() {
        return $this->errorUserTitle;
    }

    /**
     * @return string|null
     */
    public function getErrorUserMessage() {
        return $this->errorUserMessage;
    }

    /**
     * @return array|null
     */
    public function getErrorBlameFieldSpecs() {
        return $this->errorBlameFieldSpecs;
    }

}
