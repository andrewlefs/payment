<?php

namespace Misc\Http;

use Misc\Http\ReceiverInterface;

class Receiver extends \ArrayObject implements ReceiverInterface {

    /**
     * Indicates if we trust HTTP_X_FORWARDED_* headers.
     *
     * @var boolean
     */
    protected $trustForwarded = false;
    protected $headers = array();
    protected $cookies = array();
    protected $bodys = array();

    /**
     * List of query parameters that get automatically dropped when rebuilding
     * the current URL.
     */
    protected static $DROP_QUERY_PARAMS = array(
        'code',
        'state',
        'signed_request',
    );

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    protected function init() {
        $this->setBodys($this->getQueryParams());
        $this->setHeaders($this->bindingHeader());
        $this->setCookies($this->bindingCookie());
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getBodys() {
        return $this->bodys;
    }

    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    public function getCookies() {
        return $this->cookies;
    }

    public function setBodys($bodys) {
        $this->bodys = $bodys;
    }

    public function setCookies($cookies) {
        $this->cookies = $cookies;
    }

    /**
     * Trả về value cookie tương ứng name
     * @param type $cookieName string
     * @return type string
     */
    public function getCookie($cookieName) {
        if (array_key_exists($cookieName, $this->cookies)) {
            return $this->cookies[$cookieName];
        }
        return null;
    }

    /*
     * @return string
     */

    public function getUrl() {
        return $this->getHttpProtocol() . '://'
                . $this->getHttpHost() . $this->getPath()
                . ((count($this->getQueryParams()) > 0) ? ('/?'
                        . http_build_query($this->getQueryParams())) : '/');
    }

    /*
     * @return string
     */

    public function getHomeDomain() {
        return $this->getHttpProtocol() . '://'
                . $this->getHttpHost() . $this->getPath();
    }

    /*
     * @return string
     */

    public function getHostname() {
        return $this->getHttpProtocol() . '://'
                . $this->getHttpHost();
    }

    /**
     * Returns the base domain used for the cookie.
     *
     * @return string The base domain
     */
    public function getBaseDomain() {
        // The base domain is stored in the metadata cookie if not we fallback
        // to the current hostname
        $metadata = $this->getMetadataCookie();
        if (array_key_exists('base_domain', $metadata) &&
                !empty($metadata['base_domain'])) {
            return trim($metadata['base_domain'], '.');
        }
        return $this->getHttpHost();
    }

    /**
     * Returns the Current URL, stripping it of known FB parameters that should
     * not persist.
     *
     * @return string The current URL
     */
    public function getCurrentUrl() {
        $protocol = $this->getHttpProtocol() . '://';
        $host = $this->getHttpHost();
        $currentUrl = $protocol . $host . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);

        $query = '';
        if (!empty($parts['query'])) {
            // drop known fb params
            $params = explode('&', $parts['query']);
            $retained_params = array();
            foreach ($params as $param) {
                if ($this->shouldRetainParam($param)) {
                    $retained_params[] = $param;
                }
            }

            if (!empty($retained_params)) {
                $query = '?' . implode($retained_params, '&');
            }
        }

        // use port if non default
        $port = isset($parts['port']) &&
                (($protocol === 'http://' && $parts['port'] !== 80) ||
                ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }

    /**
     * 
     * @return array
     */
    public function bindingHeader() {
        $params = array();
        //var_dump($_SERVER);die;
        foreach ($_SERVER as $key => $value) {
            if (strpos(strtolower($key), "http_") === 0) {
                $params[str_replace("http_", "", strtolower($key))] = $value;
            }
        }
        return $params;
    }

    /**
     * 
     * @return array
     */
    public function bindingCookie() {
        $params = array();
        $headers = $this->bindingHeader();
        if (isset($headers["cookie"])) {
            $cookie = explode('&', str_replace("; ", "&", $headers["cookie"]));
            foreach ($cookie as $key => $value) {
                $split = explode("=", $value);
                $params[$split[0]] = urldecode($split[1]);
            }
        }
        return $params;
    }

    /**
     * Returns true if and only if the key or key/value pair should
     * be retained as part of the query string.  This amounts to
     * a brute-force search of the very small list of Facebook-specific
     * params that should be stripped out.
     *
     * @param string $param A key or key/value pair within a URL's query (e.g.
     *                      'foo=a', 'foo=', or 'foo'.
     *
     * @return boolean
     */
    protected function shouldRetainParam($param) {
        foreach (self::$DROP_QUERY_PARAMS as $drop_query_param) {
            if ($param === $drop_query_param ||
                    strpos($param, $drop_query_param . '=') === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the HTTP Host
     *
     * @return string The HTTP Host
     */
    public function getHttpHost() {
        if ($this->trustForwarded && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $forwardProxies = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
            if (!empty($forwardProxies)) {
                return $forwardProxies[0];
            }
        }
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * return array parameter data query string
     */
    public function getDomain() {
        return ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : $this->getHttpHost();
    }

    /**
     * Returns the HTTP Protocol
     *
     * @return string The HTTP Protocol
     */
    public function getHttpProtocol() {
        if ($this->trustForwarded && isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                return 'https';
            }
            return 'http';
        }
        /* apache + variants specific way of checking for https */
        if (isset($_SERVER['HTTPS']) &&
                ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) {
            return 'https';
        }
        /* nginx way of checking for https */
        if (isset($_SERVER['SERVER_PORT']) &&
                ($_SERVER['SERVER_PORT'] === '443')) {
            return 'https';
        }
        return 'http';
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        if (isset($_SERVER["REDIRECT_URL"]) && !empty($_SERVER["REDIRECT_URL"]))
            return $_SERVER["REDIRECT_URL"];
        elseif (isset($_SERVER["PATH_INFO"]) && !empty($_SERVER["PATH_INFO"]))
            return $_SERVER["PATH_INFO"];
        elseif (isset($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"]))
            return $_SERVER["REQUEST_URI"];
        elseif (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"]))
            return $_SERVER["HTTP_REFERER"];
        return '';
    }

    /**
     * @param array $data
     */
    public function enhance(array $data) {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function exportNonScalar($value) {
        return json_encode($value);
    }

    /**
     * @return array
     */
    public function export() {
        $data = array();
        foreach ($this as $key => $value) {
            $data[$key] = is_null($value) || is_scalar($value) ? $value : $this->exportNonScalar($value);
        }

        return $data;
    }

    /**
     * return array parameter data query string
     */
    public function getQueryParams() {

        $querys = null;
        if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
            $querys = $_SERVER["QUERY_STRING"];
        } elseif (isset($_SERVER["REDIRECT_QUERY_STRING"]) && !empty($_SERVER["REDIRECT_QUERY_STRING"])) {
            $querys = $_SERVER["REDIRECT_QUERY_STRING"];
        } else {
            $_SERVER["REQUEST_URI"];
        }
        $params = array();
        if ($querys == true && empty($querys) == false) {
            parse_str($querys, $parts);
            if (isset($parts['query'])) {
                $retained_params = explode('&', $parts['query']);
                $params = array_merge($params, $retained_params);
            } else {
                $params = $parts;
            }
        }              
        return $params;
    }

    /**
     * return array parameter data query string
     */
    public function getPostParams() {
        return $_POST;
    }
    
    public function getPlayload(){
        $string = file_get_contents('php://input');        
        $input = json_decode($string, true);        
        if($input == false){
            return array();
        }
        return $input;
    }

}
