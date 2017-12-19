<?php

namespace Fangcloud\Test\Api\Group;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyGroupClientTest extends AbstractApiTest
{
    static $groupId;

    public function testInit()
    {
    }

    /**
     * @depends testInit
     */
    public function testListGroups()
    {
        $response = static::$client->groups()->listGroups();
        $this->assertArrayHasKey('groups', $response);
        if (count($response['groups']) > 0) {
            static::$groupId = $response['groups'][0]['id'];
        }
    }

    /**
     * @depends testListGroups
     */
    public function testGetUsers()
    {
        if (is_null(static::$groupId)) return;
        $response = static::$client->groups()->getUsers(static::$groupId);
        $this->assertArrayHasKey('users', $response);
    }


}