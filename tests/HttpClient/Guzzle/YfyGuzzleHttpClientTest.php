<?php

namespace Fangcloud\Test\HttpClient\Guzzle;

use Fangcloud\YfyContext;
use PHPUnit\Framework\TestCase;
use Fangcloud\HttpClient\Guzzle\YfyGuzzleHttpClient;
class YfyGuzzleHttpClientTest extends TestCase
{

    public function testGuzzle() {
        $httpClient = new YfyGuzzleHttpClient();
        $response = $httpClient->send('https://open.fangcloud.com', 'GET', null, array());
    }

}