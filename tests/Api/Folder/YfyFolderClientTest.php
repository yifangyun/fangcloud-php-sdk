<?php

namespace Fangcloud\Test\Api\Folder;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyFolderClientTest extends AbstractApiTest
{
    const TEST_FOLDER_NAME = 'test';
    const TEST_FOLDER_NAME_UPDATED = 'test_new';
    const TEST_MOVE_TO_FOLDER_NAME = 'move_to_folder';
    const TEST_COPY_TO_FOLDER_NAME = 'copy_to_folder';
    static $testMoveToFolderId;
    static $testCopyToFolderId;
    static $testFolderId;

    public function testInit() {
        $response = static::$client->folders()->create(static::TEST_ROOT_FOLDER_NAME, 0);
        $this->assertArrayHasKey('id', $response);
        static::$testRootFolderId = $response['id'];
        echo "create test root folder success with id " . static::$testRootFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_MOVE_TO_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testMoveToFolderId = $response['id'];
        echo "create test move to folder success with id " . static::$testMoveToFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_COPY_TO_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testCopyToFolderId = $response['id'];
        echo "create test copy to folder success with id " . static::$testCopyToFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testFolderId = $response['id'];
        echo "create test folder success with id " . static::$testFolderId . "\n";
    }

    /**
     * @depends testInit
     */
    public function testGetInfo() {
        $response = static::$client->folders()->getInfo(static::$testFolderId);
        $this->assertArrayHasKey('id', $response);
        echo "get the folder info with id " . static::$testFolderId . "\n";
    }

    /**
     * @depends testInit
     */
    public function testUpdate() {
        $response = static::$client->folders()->update(static::$testFolderId, static::TEST_FOLDER_NAME_UPDATED);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals(static::TEST_FOLDER_NAME_UPDATED, $response['name']);
        echo "update the folder name to " . static::TEST_FOLDER_NAME_UPDATED . "\n";
    }

    /**
     * @depends testInit
     */
    public function testMoveFolder() {
        $response = static::$client->folders()->move(static::$testFolderId, static::$testMoveToFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        echo "move the folder to folder " . static::$testMoveToFolderId . "\n";
    }

    /**
     * @depends testMoveFolder
     */
    public function testChildren() {
        $response = static::$client->folders()->listChildren(static::$testRootFolderId);
        $this->assertArrayHasKey('total_count', $response);
        $this->assertEquals(2, $response['total_count']);
        echo "list children for folder " . static::$testRootFolderId . "\n";
    }

    /**
     * @depends testInit
     */
    public function testDeleteToTrash() {
        $response = static::$client->folders()->deleteToTrash(static::$testFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testDeleteToTrash
     */
    public function testTrashInfo() {
        $response = static::$client->folders()->getTrashInfo(static::$testFolderId);
        $this->assertArrayHasKey('id', $response);
        $this->expectExceptionCode('folder_deleted');
        $response = static::$client->folders()->getInfo(static::$testFolderId);
    }

    /**
     * @depends testDeleteToTrash
     */
    public function testRestoreFromTrash() {
        $response = static::$client->folders()->restoreFromTrash(static::$testFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->folders()->getInfo(static::$testFolderId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testRestoreFromTrash
     */
    public function testDeleteFromTrash() {
        $response = static::$client->folders()->deleteToTrash(static::$testFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->folders()->deleteFromTrash(static::$testFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testInit
     */
    public function testOver() {
        $response = static::$client->folders()->deleteToTrash(static::$testRootFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->folders()->deleteFromTrash(static::$testRootFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        echo "delete test root folder success\n";
    }
}