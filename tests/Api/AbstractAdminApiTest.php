<?php

namespace Fangcloud\Test\Api;

use Fangcloud\Constant\YfyJwtSubType;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyClient;
use PHPUnit\Framework\TestCase;

abstract class AbstractAdminApiTest extends TestCase
{
    /** @var YfyClient */
    protected static $client;
    const YFY_TOKEN = 'YFY_TOKEN';
    const TEST_ROOT_FOLDER_NAME = 'php-sdk-admin-test';
    const SUCCESS_RESPONSE = ['success' => true];
    protected static $testRootFolderId;

    public static function setUpBeforeClass()
    {
        YfyAppInfo::init(
            'e885b1d0-39e4-49eb-be06-16078cf3f613',
            'b366fa56-c50e-4a68-bc12-1044d974d7b8',
            'http://foo.bar/callback');

        static::$client = new YfyClient();
        $result = static::$client->oauth()->getTokenByJwtFlow(YfyJwtSubType::ENTERPRISE, 12401, 'U7TejSsByn', __DIR__ . '/../Data/private_key.pem');
        static::$client->setAccessToken($result['access_token']);
        static::$client->setRefreshToken($result['refresh_token']);
        echo 'fetch access_token: ' . $result['access_token'] . "\n";
    }
}