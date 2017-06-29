<?php

namespace Fangcloud\Test\Api\Trash;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyTrashClientTest extends AbstractApiTest
{
    public function testList() {
        $response = static::$client->trash()->listItems();
        $this->assertArrayHasKey('total_count', $response);
    }

    public function testClear() {
        $response = static::$client->trash()->clear();
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testClear
     */
    public function testRestoreAll() {
        $this->expectExceptionCode('empty_trash');
        $response = static::$client->trash()->restoreAll();
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }
}