<?php

namespace Fangcloud\Test\Api\ShareLink;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyShareLinkClientTest extends AbstractApiTest
{
    const TEST_FOLDER_NAME = 'test_share_folder';
    const TEST_FILE_NAME = 'test_share_file';
    const TEST_FILE_CONTENT = 'test';

    const ACCESS = 'public';
    const ACCESS_UPDATED = 'company';

    static $testFolderId;
    static $testFileId;
    static $dueTime;
    static $fileShareLinkUniqueName;
    static $folderShareLinkUniqueName;

    public function testInit() {
        $response = static::$client->folders()->create(static::TEST_ROOT_FOLDER_NAME, 0);
        $this->assertArrayHasKey('id', $response);
        static::$testRootFolderId = $response['id'];
        echo "create test root folder success with id " . static::$testRootFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testFolderId = $response['id'];
        echo "create test folder success with id " . static::$testFolderId . "\n";

        $response = static::$client->files()->uploadFile(static::$testRootFolderId, static::TEST_FILE_NAME, \GuzzleHttp\Psr7\stream_for(static::TEST_FILE_CONTENT));
        $this->assertArrayHasKey('id', $response);
        static::$testFileId = $response['id'];
        echo "create test file success with id " . static::$testFileId . "\n";
        static::$dueTime = date('Y-m-d', time()+60*60*24*7);
    }

    /**
     * @depends testInit
     */
    public function testCreateShareLink() {
        $response = static::$client->shareLinks()->create('folder', static::$testFolderId, static::ACCESS, static::$dueTime);
        $this->assertArrayHasKey('unique_name', $response);
        static::$folderShareLinkUniqueName = $response['unique_name'];

        $response = static::$client->shareLinks()->create('file', static::$testFileId, static::ACCESS, static::$dueTime);
        $this->assertArrayHasKey('unique_name', $response);
        static::$fileShareLinkUniqueName = $response['unique_name'];
    }

    /**
     * @depends testCreateShareLink
     */
    public function testGetInfo() {
        $response = static::$client->shareLinks()->getInfo(static::$folderShareLinkUniqueName);
        $this->assertArrayHasKey('unique_name', $response);
        $response = static::$client->shareLinks()->getInfo(static::$fileShareLinkUniqueName);
        $this->assertArrayHasKey('unique_name', $response);
    }

    /**
     * @depends testCreateShareLink
     */
    public function testUpdateShareLink() {
        $response = static::$client->shareLinks()->update(static::$folderShareLinkUniqueName, static::ACCESS_UPDATED, static::$dueTime);
        $this->assertArrayHasKey('access', $response);
        $this->assertEquals(static::ACCESS_UPDATED, $response['access']);

        $response = static::$client->shareLinks()->update(static::$fileShareLinkUniqueName, static::ACCESS_UPDATED, static::$dueTime);
        $this->assertArrayHasKey('access', $response);
        $this->assertEquals(static::ACCESS_UPDATED, $response['access']);
    }

    /**
     * @depends testCreateShareLink
     */
    public function testListChildren() {
        $response = static::$client->folders()->listShareLinks(static::$testFolderId);
        $this->assertArrayHasKey('share_links', $response);
        $this->assertTrue(is_array($response['share_links']));
        $this->assertEquals(1, count($response['share_links']));

        $response = static::$client->files()->listShareLinks(static::$testFileId);
        $this->assertArrayHasKey('share_links', $response);
        $this->assertTrue(is_array($response['share_links']));
        $this->assertEquals(1, count($response['share_links']));
    }

    /**
     * @depends testCreateShareLink
     */
    public function testDeleteFolderShareLink() {
        $response = static::$client->shareLinks()->revoke(static::$folderShareLinkUniqueName);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $this->expectExceptionCode('share_link_not_found_or_deleted');
        $response = static::$client->shareLinks()->getInfo(static::$folderShareLinkUniqueName);
    }

    /**
     * @depends testCreateShareLink
     */
    public function testDeleteFileShareLink() {
        $response = static::$client->shareLinks()->revoke(static::$fileShareLinkUniqueName);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $this->expectExceptionCode('share_link_not_found_or_deleted');
        $response = static::$client->shareLinks()->getInfo(static::$fileShareLinkUniqueName);
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