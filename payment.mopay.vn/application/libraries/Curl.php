<?php

class Curl {

    var $core;
    var $header = false;
    var $useragent;
    var $referer = false;
    var $followlocation;
    var $ssl = false;
    var $pathcookie;

    public function __construct() {
        $this->useragent = $_SERVER['HTTP_USER_AGENT'];
    }

    private function request($method, $url, $vars) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followlocation);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->pathcookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->pathcookie);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        if ($data) {
            if ($this->callback) {
                $callback = $this->callback;
                $this->callback = false;
                return call_user_func($callback, $data);
            } else {
                return $data;
            }
        } else {
            return @curl_error($ch);
        }
    }

    public function setheader($boolean = true) {
        if ($boolean) {
            $this->header = true;
        } else {
            $this->header = false;
        }
        return true;
    }

    public function setuseragent($agent = false) {
        if ($agent) {
            $this->useragent = $agent;
        } else {
            $this->useragent = '';
        }
        return true;
    }

    public function setreferer($referer = false) {
        if ($referer) {
            $this->referer = $referer;
        } else {
            $this->referer = '';
        }
        return true;
    }

    public function setfollowlocation($boolean = false) {
        if ($followlocation) {
            $this->followlocation = true;
        } else {
            $this->followlocation = false;
        }
        return true;
    }

    public function setssl($boolean = true) {
        if ($boolean) {
            $this->ssl = false;
        } else {
            $this->ssl = true;
        }
        return true;
    }

    public function setpathcookie($path) {
        if (empty($path)) {
            return false;
        }
        $this->pathcookie = $path;
        return true;
    }

    public function get($url) {
        if (empty($url)) {
            return false;
        }
        return $this->request('GET', $url, 'NULL');
    }

    public function post($url, $vars) {
        if (empty($url) || empty($vars)) {
            return false;
        }
        return $this->request('POST', $url, $vars);
    }

    public function tranload($pathFileName, $linktranload) {
        $handle = @fopen($linktranload, "rb");
        $contents = @stream_get_contents($handle);
        @fclose($handle);
        $f2 = @fopen($pathFileName, "w");
        @fwrite($f2, $contents);
        @fclose($f2);
        if (file_exists($pathFileName) && filesize($pathFileName)) {
            return filesize($pathFileName);
        } else {
            @unlink($pathFileName);
            return false;
        }
    }

    public function writefile($filename, $content) {
        $fh = fopen($filename, "wb");
        fwrite($fh, $content);
        fclose($fh);
    }

}

?>