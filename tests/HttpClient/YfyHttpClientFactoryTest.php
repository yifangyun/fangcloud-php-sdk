<?php

namespace Fangcloud\Test\HttpClient;

use Fangcloud\HttpClient\YfyHttpClientFactory;
use Fangcloud\YfyContext;
use PHPUnit\Framework\TestCase;

class YfyHttpClientFactoryTest extends TestCase
{

    /**
     * @expectedException
     */
    public function testFactory() {
        $httpClient = YfyHttpClientFactory::createHttpClient();
        $this->assertInstanceOf('Fangcloud\HttpClient\YfyHttpClient', $httpClient);

        $httpClient = YfyHttpClientFactory::createHttpClient('guzzle');
        $this->assertInstanceOf('Fangcloud\HttpClient\Guzzle\YfyGuzzleHttpClient', $httpClient);

    }

}