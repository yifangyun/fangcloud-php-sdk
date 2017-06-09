<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/2
 * Time: 16:38
 */

namespace Fangcloud\HttpClient;

use Exception;
use Fangcloud\HttpClient\Curl\YfyCurlHttpClient;
use Fangcloud\HttpClient\Guzzle\YfyGuzzleHttpClient;
use Fangcloud\HttpClient\Stream\YfyStreamHttpClient;
use InvalidArgumentException;


/**
 * Class YfyHttpClientFactory
 * @package Fangcloud\HttpClient
 */
class YfyHttpClientFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param string|null $handler
     * @return YfyHttpClient If the cURL extension or the Guzzle client aren't available (if required).
     * @throws Exception If the cURL extension or the Guzzle client aren't available (if required).
     */
    public static function createHttpClient($handler = null)
    {

        if (!class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }

        return new YfyGuzzleHttpClient();
    }

}