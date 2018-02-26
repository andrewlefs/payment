<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Misc\Http;

use Misc\Http\RequestInterface;

class ClientCurl {

    const DEFAULT_LAST_LEVEL_DOMAIN = "grahp";
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function sendRequest(RequestInterface $request) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request->getUrl());

        $method = $request->getMethod();
        if ($method !== RequestInterface::METHOD_GET && $method !== RequestInterface::METHOD_POST) {
            $curlopts[CURLOPT_CUSTOMREQUEST] = $method;
        } elseif ($method === RequestInterface::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getQueryParams());
        }

        curl_setopt($ch, CURLOPT_HEADER, true);
        if ($request->getHeaders()->count()) {
            $headers = array();
            foreach ($request->getHeaders() as $header => $value) {
                $headers[] = "{$header}: {$value}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER["REMOTE_ADDR"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}
