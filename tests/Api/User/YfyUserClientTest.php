<?php

namespace Fangcloud\Test\Api\User;

use Fangcloud\Test\Api\AbstractApiTest;

class YfyUserClientTest extends AbstractApiTest
{
    static $selfUserId;
    static $anotherUserId;
    static $originName;
    static $updatedName = 'tt new';
    static $selfProfileKey;

    public function testSelfInfo() {
        $response = static::$client->users()->getSelf();
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('profile_pic_key', $response);
        static::$selfUserId = $response['id'];
        static::$originName = $response['name'];
        static::$selfProfileKey = $response['profile_pic_key'];
    }

    /**
     * @depends testSelfInfo
     */
    public function testSearchUser() {
        $response = static::$client->users()->searchUser();
        $this->assertArrayHasKey('users', $response);
        $this->assertTrue(is_array($response['users']));
        foreach ($response['users'] as $user) {
            if ($user['id'] !== static::$selfUserId) {
                static::$anotherUserId = $user['id'];
                break;
            }
        }
    }

    /**
     * @depends testSelfInfo
     */
    public function testUserInfo() {
        $response = static::$client->users()->getUser(static::$selfUserId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testSelfInfo
     */
    public function testUpdateSelf() {
        $response = static::$client->users()->updateSelf(static::$updatedName);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals(static::$updatedName, $response['name']);

        $response = static::$client->users()->updateSelf(static::$originName);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals(static::$originName, $response['name']);
    }

    /**
     * @depends testSelfInfo
     */
    public function testDownloadProfilePic() {
        $downloadProfilePic = static::$client->users()->downloadProfilePic(static::$selfUserId, static::$selfProfileKey);
        $this->assertNotTrue($downloadProfilePic->getStream()->eof());
    }

    /**
     * @depends testSelfInfo
     */
    public function testGetSpaceUsage()
    {
        $response = static::$client->users()->getSpaceUsage();
        $this->assertArrayHasKey('space_used', $response);
        $this->assertArrayHasKey('space_total', $response);
    }

}