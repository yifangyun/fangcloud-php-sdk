<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/2
 * Time: 16:46
 */

namespace Fangcloud\HttpClient;

use Fangcloud\YfyRequest;


/**
 * Interface YfyHttpClientInterface
 * @package Fangcloud\HttpClient
 */
interface YfyHttpClient
{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param YfyRequest $yfyRequest
     *
     * @return \Fangcloud\Http\YfyRawResponse Raw response from the server.
     *
     * @throws \Fangcloud\Exception\YfySDKException
     */
    public function send(YfyRequest $yfyRequest);
}