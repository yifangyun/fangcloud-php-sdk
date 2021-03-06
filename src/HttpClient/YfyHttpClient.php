<?php
/**
 * HttpClient基类
 * 可以有不同实现
 */

namespace Fangcloud\HttpClient;

use Fangcloud\Http\YfyRequest;


/**
 * Interface YfyHttpClientInterface
 * @package Fangcloud\HttpClient
 */
interface YfyHttpClient
{
    /**
     * 发送一个请求
     *
     * @param YfyRequest $yfyRequest 封装的发送请求
     * @return \Fangcloud\Http\YfyRawResponse 封装的请求返回
     * @throws \Fangcloud\Exception\YfySDKException
     */
    public function send(YfyRequest $yfyRequest);
}