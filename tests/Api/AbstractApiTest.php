<?php

namespace Fangcloud\Test\Api;

use Fangcloud\YfyAppInfo;
use Fangcloud\YfyClient;
use PHPUnit\Framework\TestCase;

abstract class AbstractApiTest extends TestCase
{
    /** @var YfyClient */
    protected static $client;
    const YFY_TOKEN = 'YFY_TOKEN';
    const TEST_ROOT_FOLDER_NAME = 'php-sdk-test';
    const SUCCESS_RESPONSE = ['success' => true];
    protected static $testRootFolderId;

    public static function setUpBeforeClass()
    {
        YfyAppInfo::init('fake-client-id', 'fake-client-secret', 'http://foo.bar/callback');
        $accessToken = getenv(static::YFY_TOKEN);
        $options = [
            'access_token' => $accessToken
        ];
        static::$client = new YfyClient($options);
    }
}