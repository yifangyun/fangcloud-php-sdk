<?php

namespace Fangcloud\Test\Api\Department;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyDepartmentClientTest extends AbstractApiTest
{

    public function testInit()
    {
    }

    /**
     * @depends testInit
     */
    public function testGetDepartmentInfo()
    {
        $response = static::$client->departments()->getInfo(0);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testInit
     */
    public function testGetChildren()
    {
        $response = static::$client->departments()->getChildren(0);
        $this->assertArrayHasKey('children', $response);
    }

    /**
     * @depends testInit
     */
    public function testGetUsers()
    {
        $response = static::$client->departments()->getUsers(0);
        $this->assertArrayHasKey('users', $response);
    }
}