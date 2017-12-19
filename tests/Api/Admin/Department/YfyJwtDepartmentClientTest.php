<?php

namespace Fangcloud\Test\Api\Admin\Department;


use Fangcloud\Test\Api\AbstractAdminApiTest;

class YfyJwtDepartmentClientTest extends AbstractAdminApiTest
{
    static $departmentId;
    static $subDepartmentId;
    static $userId;

    public function testGetChildren() {
        $response = static::$client->admin()->departments()->getChildren(0);
        $this->assertArrayHasKey('children', $response);
    }

    public function testGetUsers() {
        $response = static::$client->admin()->departments()->getUsers(0);
        $this->assertArrayHasKey('users', $response);
        static::$userId = $response['users'][0]['id'];
    }

    public function testCreate()
    {
        $response = static::$client->admin()->departments()->create('php-sdk-test-department', 0);
        $this->assertArrayHasKey('id', $response);
        static::$departmentId = $response['id'];
        $response = static::$client->admin()->departments()->create('php-sdk-test-sub-department', static::$departmentId);
        $this->assertArrayHasKey('id', $response);
        static::$subDepartmentId = $response['id'];
    }

    /**
     * @depends testCreate
     */
    public function testGetInfo() {
        $response = static::$client->admin()->departments()->getInfo(static::$departmentId);
        $this->assertArrayHasKey('id', $response);
        $response = static::$client->admin()->departments()->getInfo(static::$subDepartmentId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate() {
        $updatedName = 'php-sdk-test-department2';
        $response = static::$client->admin()->departments()->update(static::$departmentId, $updatedName);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals($updatedName, $response['name']);
    }

    /**
     * @depends testCreate
     * @depends testGetUsers
     */
    public function testAddUser() {
        $response = static::$client->admin()->departments()->addUser(static::$departmentId, static::$userId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testAddUser
     */
    public function testRemoveUser() {
        $response = static::$client->admin()->departments()->removeUser(static::$departmentId, static::$userId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testCreate
     */
    public function testOver()
    {
        $deleteUri = '/api/v2/admin/department/%s/delete';
        $response = static::$client->custom()->post(sprintf($deleteUri, static::$subDepartmentId));
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->custom()->post(sprintf($deleteUri, static::$departmentId));
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }


}