<?php

namespace Fangcloud\Test\Api\File;

use Fangcloud\Test\Api\AbstractApiTest;

class YfyFileClientTest extends AbstractApiTest
{
    const TEST_FILENAME = 'test.txt';
    const TEST_FILENAME_UPDATED = 'test_new.txt';
    const TEST_FILENAME_NEW_VERSION = 'test_new2.txt';
    const TEST_FILE_CONTENT = 'test';
    const TEST_MOVE_TO_FOLDER_NAME = 'move_to_folder';
    const TEST_COPY_TO_FOLDER_NAME = 'copy_to_folder';
    static $testMoveToFolderId;
    static $testCopyToFolderId;
    static $testFileId;
    static $testFileVersionId;

    public function testInit() {
        $response = static::$client->folders()->create(static::TEST_ROOT_FOLDER_NAME, 0);
        $this->assertArrayHasKey('id', $response);
        static::$testRootFolderId = $response['id'];
        //echo "create test root folder success with id " . static::$testRootFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_MOVE_TO_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testMoveToFolderId = $response['id'];
        //echo "create test move to folder success with id " . static::$testMoveToFolderId . "\n";

        $response = static::$client->folders()->create(static::TEST_COPY_TO_FOLDER_NAME, static::$testRootFolderId);
        $this->assertArrayHasKey('id', $response);
        static::$testCopyToFolderId = $response['id'];
        //echo "create test copy to folder success with id " . static::$testCopyToFolderId . "\n";
    }

    /**
     * @depends testInit
     */
    public function testUploadFile() {
        $response = static::$client->files()->uploadFile(static::$testRootFolderId, static::TEST_FILENAME, \GuzzleHttp\Psr7\stream_for(static::TEST_FILE_CONTENT));
        $this->assertArrayHasKey('id', $response);
        static::$testFileId = $response['id'];
        //echo "upload a file with returned id " . static::$testFileId . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testFileInfo() {
        $response = static::$client->files()->getInfo(static::$testFileId);
        $this->assertArrayHasKey('id', $response);
        //echo "get the file info with id " . static::$testFileId . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testUpdateFile() {
        $response = static::$client->files()->update(static::$testFileId, static::TEST_FILENAME_UPDATED);
        $this->assertArrayHasKey('name', $response);
        $this->assertEquals(static::TEST_FILENAME_UPDATED . '.txt', $response['name']);
        //echo "update the file name to " . static::TEST_FILENAME_UPDATED . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testMoveFile() {
        $response = static::$client->files()->move(static::$testFileId, static::$testMoveToFolderId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        //echo "move the file to folder " . static::$testMoveToFolderId . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testCopyFile() {
        $response = static::$client->files()->copy(static::$testFileId, static::$testCopyToFolderId);
        $this->assertArrayHasKey('id', $response);
        //echo "copy the file to folder " . static::$testCopyToFolderId . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testDownload() {
        $downloadFile = static::$client->files()->download(static::$testFileId);
        $this->assertEquals(static::TEST_FILE_CONTENT, $downloadFile->getStream()->getContents());
        //echo "download file success with id " . static::$testFileId . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testUploadNewVersion() {
        $response = static::$client->files()->uploadNewVersion(static::$testFileId, static::TEST_FILENAME_NEW_VERSION, 'fake remark', \GuzzleHttp\Psr7\stream_for(static::TEST_FILE_CONTENT));
        $this->assertArrayHasKey('id', $response);
        //echo "upload new version with id " . static::$testFileId . "\n";
    }

    /**
     * @depends testUploadNewVersion
     */
    public function testFileVersions() {
        $response = static::$client->files()->listVersions(static::$testFileId);
        $this->assertArrayHasKey('file_versions', $response);
        $this->assertTrue(is_array($response['file_versions']));
        $this->assertEquals(2, count($response['file_versions']));
        $this->assertArrayHasKey('current', $response['file_versions'][0]);
        $this->assertArrayHasKey('current', $response['file_versions'][1]);
        $this->assertEquals(true, $response['file_versions'][0]['current']);
        $this->assertEquals(false, $response['file_versions'][1]['current']);
        $this->assertArrayHasKey('id', $response['file_versions'][1]);
        static::$testFileVersionId = $response['file_versions'][1]['id'];
        //echo "get the file versions with id " . static::$testFileId . ". get an old version with id " . static::$testFileVersionId . "\n";
    }

    /**
     * @depends testFileVersions
     */
    public function testFileVersionInfo() {
        $response = static::$client->files()->getVersionInfo(static::$testFileId, static::$testFileVersionId);
        $this->assertArrayHasKey('id', $response);
        //echo "get the file version info with file id " . static::$testFileId . " version id " . static::$testFileVersionId . "\n";
    }

    /**
     * @depends testFileVersions
     */
    public function testPromoteVersion() {
        $response = static::$client->files()->promoteVersion(static::$testFileId, static::$testFileVersionId);
        $this->assertArrayHasKey('id', $response);
        $response = static::$client->files()->listVersions(static::$testFileId);
        $this->assertArrayHasKey('file_versions', $response);
        $this->assertTrue(is_array($response['file_versions']));
        $this->assertEquals(3, count($response['file_versions']));
        //echo "promote file version with id " . static::$testFileId . " and version id " . static::$testFileVersionId . "\n";
    }

    /**
     * @depends testPromoteVersion
     */
    public function testDeleteVersion() {
        $response = static::$client->files()->deleteVersion(static::$testFileId, static::$testFileVersionId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->files()->listVersions(static::$testFileId);
        $this->assertArrayHasKey('file_versions', $response);
        $this->assertTrue(is_array($response['file_versions']));
        $this->assertEquals(2, count($response['file_versions']));
        //echo "delete file version with id " . static::$testFileId . " and version id " . static::$testFileVersionId . "\n";
    }

    /**
     * @depends testUploadFile
     */
    public function testDeleteToTrash() {
        $response = static::$client->files()->deleteToTrash(static::$testFileId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
    }

    /**
     * @depends testDeleteToTrash
     */
    public function testTrashInfo() {
        $response = static::$client->files()->getTrashInfo(static::$testFileId);
        $this->assertArrayHasKey('id', $response);
        $this->expectExceptionCode('file_deleted');
        $response = static::$client->files()->getInfo(static::$testFileId);
    }

    /**
     * @depends testDeleteToTrash
     */
    public function testRestoreFromTrash() {
        $response = static::$client->files()->restoreFromTrash(static::$testFileId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->files()->getInfo(static::$testFileId);
        $this->assertArrayHasKey('id', $response);
    }

    /**
     * @depends testRestoreFromTrash
     */
    public function testDeleteFromTrash() {
        $response = static::$client->files()->deleteToTrash(static::$testFileId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);
        $response = static::$client->files()->deleteFromTrash(static::$testFileId);
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
        //echo "delete test root folder success\n";
    }

}