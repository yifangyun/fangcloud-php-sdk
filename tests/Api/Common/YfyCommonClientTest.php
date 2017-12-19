<?php

namespace Fangcloud\Test\Api\Common;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyCommonClientTest extends AbstractApiTest
{

    public function testGet()
    {
        $response = static::$client->custom()->get('/api/v2/user/info');
        $this->assertArrayHasKey('id', $response);
    }

    public function testPost() {
        $this->expectExceptionCode('file_not_found');
        $response = static::$client->custom()->post('/api/v2/file/123/delete');
    }
}