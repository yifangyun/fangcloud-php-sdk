<?php

use Fangcloud\Api\User\YfyUserClient;
use Fangcloud\HttpClient\YfyHttpClientFactory;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use PHPUnit\Framework\TestCase;

class YfyUserClientTest extends TestCase
{
    public function testUserClient() {
        YfyAppInfo::init(null, null);
        $context = new YfyContext();
        $context->setAccessToken('a000d8bf-0bfe-4233-af2d-635fef7bfac8');
        $context->setRefreshToken('8c3c36df-a028-4d80-a896-54bfdb7f308c');
        $httpClient = YfyHttpClientFactory::createHttpClient('guzzle');
        $client = new YfyUserClient($context, $httpClient);
        $user = $client->getSelf();

        $result = $client->downloadProfilePic($user['id'], $user['profile_pic_key'], '/Users/just-cj/tmp');
    }
}