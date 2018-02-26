<?php

namespace Misc\Http;

interface ReceiverInterface {

    public function getHeaders();

    public function getBodys();

    public function setHeaders($headers);

    public function setBodys($bodys);

    /**
     * 
     * @return array
     */
    public function bindingHeader();

    /**
     * @param array $data
     */
    public function enhance(array $data);

    /**
     * @return array
     */
    public function export();

    /**
     * return array parameter data query string
     */
    public function getQueryParams();

    public function getUrl();

    /**
     * return array parameter data query string
     */
    public function getPostParams();
    
    /**
     * return array parameter data query string
     */
    public function getHostname();
    
    /**
     * return array parameter data query string
     */
    public function getDomain();
}
