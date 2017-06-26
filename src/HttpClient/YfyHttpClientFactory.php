<?php
/**
 * HttpClient工厂
 */

namespace Fangcloud\HttpClient;

use Exception;
use Fangcloud\HttpClient\Guzzle\YfyGuzzleHttpClient;


/**
 * Class YfyHttpClientFactory
 * @package Fangcloud\HttpClient
 */
class YfyHttpClientFactory
{
    /**
     * YfyHttpClientFactory constructor.
     */
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * 创建一个http client
     *
     * @param string|null $handler
     * @return YfyHttpClient
     * @throws Exception
     */
    public static function createHttpClient($handler = null)
    {
        if (!class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }
        return new YfyGuzzleHttpClient();
    }

}