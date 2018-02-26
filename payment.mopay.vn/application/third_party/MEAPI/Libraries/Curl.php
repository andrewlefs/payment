<?php

class Curl
{

    var $core;
    var $header = FALSE;
    var $useragent;
    var $referer = FALSE;
    var $followlocation;
    var $ssl = FALSE;
    var $pathcookie;
    protected $CI;

    public function __construct()
    {
        $this->useragent = $_SERVER['HTTP_USER_AGENT'];
        $this->CI = &get_instance();

    }

    public function getInfo($ch, $opt = 0)
    {
        return curl_getinfo($ch, $opt);
    }

    private function extractResponseHeadersAndBody($ch, $raw_response)
    {
        $header_size = $this->getInfo($ch);

        $raw_headers = mb_substr($raw_response, 0, $header_size);
        $raw_body = mb_substr($raw_response, $header_size);

        return array(trim($raw_headers), trim($raw_body));

    }

    /**
     * @param Headers $headers
     * @param string $raw_headers
     */
    protected function parseHeaders($raw_headers) {
        $raw_headers = str_replace("\r\n", "\n", $raw_headers);
        $headers = array();
        // There will be multiple headers if a 301 was followed
        // or a proxy was followed, etc
        $header_collection = explode("\n\n", trim($raw_headers));
        // We just want the last response (at the end)
        $raw_headers = array_pop($header_collection);

        $header_components = explode("\n", $raw_headers);
        foreach ($header_components as $line) {
            if (strpos($line, ': ') === false) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        if($headers['http_code']){
            return $headers['http_code'];
        }else{
            return $headers;
        }

    }

    private function request($method, $url, $vars)
    {
        $ch = curl_init();
        $header = array();
        if (!empty($vars['header'])) {
            $header = $vars['header'];
            unset($vars['header']);
            $this->header = true;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, $this->header);
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
            var_dump($data);
            $response = $this->extractResponseHeadersAndBody($ch, $data);
            echo "<br/>";
            var_dump($response);
            echo "<br/>";
            $data = $this->parseHeaders($response[1]);

            var_dump($data);
            if ($this->callback) {
                $callback = $this->callback;
                $this->callback = FALSE;
                return call_user_func($callback, $data);
            } else {
                return $data;
            }
        } else {
            return @curl_error($ch);
        }
    }

    public function setheader($boolean = TRUE)
    {
        if ($boolean) {
            $this->header = TRUE;
        } else {
            $this->header = FALSE;
        }
        return TRUE;
    }

    public function setuseragent($agent = FALSE)
    {
        if ($agent) {
            $this->useragent = $agent;
        } else {
            $this->useragent = '';
        }
        return TRUE;
    }


    public function setreferer($referer = FALSE)
    {
        if ($referer) {
            $this->referer = $referer;
        } else {
            $this->referer = '';
        }
        return TRUE;
    }

    public function setfollowlocation($boolean = FALSE)
    {
        if ($boolean) {
            $this->followlocation = TRUE;
        } else {
            $this->followlocation = FALSE;
        }
        return TRUE;
    }

    public function setssl($boolean = TRUE)
    {
        if ($boolean) {
            $this->ssl = FALSE;
        } else {
            $this->ssl = TRUE;
        }
        return TRUE;
    }

    public function setpathcookie($path)
    {
        if (empty($path)) {
            $this->pathcookie = FALSE;
            return FALSE;
        }
        $this->pathcookie = $path;
        return TRUE;
    }

    public function get($url)
    {
        if (empty($url)) {
            return FALSE;
        }
        $this->CI->benchmark->mark('request_start');
        $html = $this->request('GET', $url, 'NULL');
        $this->CI->benchmark->mark('request_end');
        $time_execute = $this->CI->benchmark->elapsed_time('request_start', 'request_end');
        MEAPI_Log::writeCsv(array('REQUEST API', $time_execute, $url, $html), 'other');
        return $html;
    }

    public function post($url, $vars)
    {
        if (empty($url) || empty($vars)) {
            return FALSE;
        }
        $this->CI->benchmark->mark('request_start');
        $html = $this->request('POST', $url, $vars);
        $this->CI->benchmark->mark('request_end');
        $time_execute = $this->CI->benchmark->elapsed_time('request_start', 'request_end');
        MEAPI_Log::writeCsv(array('REQUEST API', $time_execute, $url . http_build_query($vars), $html), 'other');
        return $html;
    }

    public function tranload($pathFileName, $linktranload)
    {
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
            return FALSE;
        }
    }

    public function writefile($filename, $content)
    {
        $fh = fopen($filename, "wb");
        fwrite($fh, $content);
        fclose($fh);
    }

}

?>