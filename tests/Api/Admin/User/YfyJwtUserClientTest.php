<?php

namespace Fangcloud\Test\Api\User;


use Fangcloud\Constant\YfyIdentifierType;
use Fangcloud\Test\Api\AbstractAdminApiTest;

class YfyJwtUserClientTest extends AbstractAdminApiTest
{
    static $userId;

    public function testCreate()
    {
        $response = static::$client->admin()->users()->create(YfyIdentifierType::EMAIL, 'php-sdk-test@test.com');
        $this->assertArrayHasKey('id', $response);
        static::$userId = $response['id'];
    }

    /**
     * @depends testCreate
     */
    public function testGetInfo() {
        $response = static::$client->admin()->users()->getUser(static::$userId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate() {
        $updatedName = '新名字';
        $response = static::$client->admin()->users()->update(static::$userId, $updatedName);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals($updatedName, $response['name']);
    }

    /**
     * @depends testCreate
     */
    public function testGetLoginUrl() {
        $response = static::$client->admin()->users()->getLoginUrl(YfyIdentifierType::EMAIL, 'php-sdk-test@test.com');
        $this->assertArrayHasKey('login_url', $response);
        echo $response['login_url'] . "\n";
    }

    /**
     * @depends testCreate
     */
    public function testOver() {
        $deleteUserUri = '/api/v2/admin/user/%s/delete';
        $response = static::$client->custom()->post(sprintf($deleteUserUri, static::$userId));
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }
}