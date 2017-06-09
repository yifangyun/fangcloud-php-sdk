<?php

use Fangcloud\Api\File\YfyFileClient;
use Fangcloud\HttpClient\YfyHttpClientFactory;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use PHPUnit\Framework\TestCase;

class YfyFileClientTest extends TestCase
{
//    public function testUploadFile() {
//        YfyAppInfo::init(null, null);
//        $context = new YfyContext();
//        $context->setAccessToken('a000d8bf-0bfe-4233-af2d-635fef7bfac8');
//        $context->setRefreshToken('8c3c36df-a028-4d80-a896-54bfdb7f308c');
//        $httpClient = YfyHttpClientFactory::createHttpClient('guzzle');
//        $client = new YfyFileClient($context, $httpClient);
//        $client->uploadFile(475000032006, 'test_'. time() . '.jpg', '/Users/just-cj/Pictures/Collection/K站/Konachan.com - 224565 crimsonseed ia vocaloid.jpg');
//    }

    public function testDownloadFile() {
        YfyAppInfo::init(null, null);
        $context = new YfyContext();
        $context->setAccessToken('a000d8bf-0bfe-4233-af2d-635fef7bfac8');
        $context->setRefreshToken('8c3c36df-a028-4d80-a896-54bfdb7f308c');
        $httpClient = YfyHttpClientFactory::createHttpClient('guzzle');
        $client = new YfyFileClient($context, $httpClient);
        $client->download(501001800390, '/Users/just-cj/tmp');
    }
}