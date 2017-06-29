<?php

namespace Fangcloud\Test\Api\Item;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyItemClientTest extends AbstractApiTest
{
    public function testSearch() {
        $response = static::$client->items()->search('sdk');
        $this->assertArrayHasKey('total_count', $response);
    }
}