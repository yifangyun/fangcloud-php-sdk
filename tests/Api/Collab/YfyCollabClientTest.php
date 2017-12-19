<?php

namespace Fangcloud\Test\Api\Collab;


use Fangcloud\Constant\YfyCollabSubType;
use Fangcloud\Test\Api\AbstractApiTest;

class YfyCollabClientTest extends AbstractApiTest
{
    const TEST_FOLDER_NAME = 'test_collab_folder';
    const ROLE = 'previewer';
    const ROLE_UPDATED = 'editor';
    static $testFolderId;

    static $selfUserId;
    static $anotherUserId;

    static $testCollabId;

    public function testInit() {
        $response = static::$client->folders()->create(static::TEST_ROOT_FOLDER_NAME, 0);
        $this->assertArrayHasKey('id', $response);
        static::$testRootFolderId = $response['id'];
        //echo "create test root folder success with id " . static::$testRootFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testFolderId = $response['id'];
        //echo "create test folder success with id " . static::$testFolderId . "\n";

        $response = static::$client->users()->getSelf();
        $this->assertArrayHasKey('id', $response);
        static::$selfUserId = $response['id'];

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
     * @depends testInit
     */
    public function testCollabInvite() {
        $response = static::$client->collabs()->invite(static::$testFolderId, YfyCollabSubType::USER, static::$anotherUserId, static::ROLE);
        $this->assertArrayHasKey('id', $response);
        static::$testCollabId = $response['id'];
    }

    /**
     * @depends testCollabInvite
     */
    public function testCollabInfo() {
        $response = static::$client->collabs()->getInfo(static::$testCollabId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testCollabInvite
     */
    public function testUpdateCollab() {
        $response = static::$client->collabs()->update(static::$testCollabId, static::ROLE_UPDATED);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('role', $response);
        $this->assertEquals(static::ROLE_UPDATED, $response['role']);
    }

    /**
     * @depends testCollabInvite
     */
    public function testListFolderCollabs() {
        $response = static::$client->folders()->listCollabs(static::$testFolderId);
        $this->assertArrayHasKey('collabs', $response);
        $this->assertTrue(is_array($response['collabs']));
        $this->assertEquals(2, count($response['collabs']));
    }

    /**
     * @depends testCollabInvite
     */
    public function testDeleteCollab() {
        $response = static::$client->collabs()->delete(static::$testCollabId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $this->expectExceptionCode('collab_not_found');
        $response = static::$client->collabs()->getInfo(static::$testCollabId);
    }

    /**
     * @depends testInit
     */
    public function testOver() {
        $response = static::$client->folders()->deleteToTrash(static::$testRootFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->folders()->deleteFromTrash(static::$testRootFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        //echo "delete test root folder success\n";
    }
}