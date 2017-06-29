<?php

namespace Fangcloud\Test;


use Fangcloud\PersistentData\NoopPersistentDataHandler;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyClient;
use PHPUnit\Framework\TestCase;

class YfyClientTest extends TestCase
{
    /**
     * 测试没有初始化的情况
     */
    public function testYfyClientWithoutInit() {
        YfyAppInfo::reset();
        $this->expectException('Fangcloud\Exception\YfySdkException');
        new YfyClient();
    }

    /**
     * 测试全部使用默认参数的情况
     */
    public function testDefaultYfyClient() {
        YfyAppInfo::init('xxx', 'xxx', 'xxx');
        $client = new YfyClient();
    }

    /**
 * 测试没有开启session下初始化的情况
 */
    public function testNoopPersistentDataHandler() {
        $this->expectException('Fangcloud\Exception\YfySdkException');
        $this->expectExceptionMessage(NoopPersistentDataHandler::ERROR_MESSAGE);
        YfyAppInfo::init('xxx', 'xxx', 'xxx');
        $client = new YfyClient();
        $client->oauth()->getAuthorizationUrl();
    }

    /**
     * 测试开启session下初始化的情况
     */
    public function testDefaultSessionPersistentDataHandler() {
        @session_start();
        YfyAppInfo::init('xxx', 'xxx', 'xxx');
        $client = new YfyClient();
        $client->oauth()->getAuthorizationUrl('xxx');
    }

}