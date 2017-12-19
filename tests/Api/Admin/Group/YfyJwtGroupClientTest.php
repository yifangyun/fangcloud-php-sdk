<?php

namespace Fangcloud\Test\Api\Group;


use Fangcloud\Test\Api\AbstractAdminApiTest;

class YfyJwtGroupClientTest extends AbstractAdminApiTest
{
    static $groupId;
    static $userId;

    public function testCreate()
    {
        $response = static::$client->admin()->groups()->create('php-sdk-test-group');
        $this->assertArrayHasKey('id', $response);
        static::$groupId = $response['id'];
    }

    /**
     * @depends testCreate
     */
    public function testGetInfo() {
        $response = static::$client->admin()->groups()->getInfo(static::$groupId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate() {
        $updatedName = 'php-sdk-test-group2';
        $response = static::$client->admin()->groups()->update(static::$groupId, $updatedName);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals($updatedName, $response['name']);
    }

    /**
     * @depends testCreate
     */
    public function testAddUser() {
        $response = static::$client->admin()->departments()->getUsers(0);
        $this->assertArrayHasKey('users', $response);
        static::$userId = $response['users'][0]['id'];

        $response = static::$client->admin()->groups()->addUser(static::$groupId, static::$userId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testAddUser
     */
    public function testGetUsers() {
        $response = static::$client->admin()->groups()->getUsers(static::$groupId);
        $this->assertArrayHasKey('users', $response);
        $this->assertEquals(1, count($response['users']));
        $this->assertEquals(static::$userId, $response['users'][0]['id']);
    }

    /**
     * @depends testGetUsers
     */
    public function testRemoveUser() {
        $response = static::$client->admin()->groups()->removeUser(static::$groupId, static::$userId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testCreate
     */
    public function testListGroups() {
        $response = static::$client->admin()->groups()->listGroups();
        $this->assertArrayHasKey('groups', $response);
    }

    /**
     * @depends testCreate
     */
    public function testOver() {
        $response = static::$client->admin()->groups()->delete(static::$groupId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }
}