<?php

namespace Fangcloud\Test\Api\Comment;


use Fangcloud\Test\Api\AbstractApiTest;

class YfyCommentClientTest extends AbstractApiTest
{
    const TEST_FILE_NAME = 'test';
    const TEST_FILE_CONTENT = 'test';
    const TEST_COMMENT_MESSAGE = 'test message';
    static $testFileId;
    static $testCommentId;

    public function testInit() {
        $response = static::$client->folders()->create(static::TEST_ROOT_FOLDER_NAME, 0);
        $this->assertArrayHasKey('id', $response);
        static::$testRootFolderId = $response['id'];
        //echo "create test root folder success with id " . static::$testRootFolderId . "\n";

        $response = static::$client->files()->uploadFile(static::$testRootFolderId, static::TEST_FILE_NAME, \GuzzleHttp\Psr7\stream_for(static::TEST_FILE_CONTENT));
        $this->assertArrayHasKey('id', $response);
        static::$testFileId = $response['id'];
        //echo "upload test file success with id " . static::$testFileId . "\n";
    }

    /**
     * @depends testInit
     */
    public function testCreateComment() {
        $response = static::$client->comments()->create(static::$testFileId, static::TEST_COMMENT_MESSAGE);
        $this->assertArrayHasKey('comment_id', $response);
        static::$testCommentId = $response['comment_id'];
    }

    /**
     * @depends testCreateComment
     */
    public function testListFileComments() {
        $response = static::$client->files()->listComments(static::$testFileId);
        $this->assertArrayHasKey('comments', $response);
        $this->assertTrue(is_array($response['comments']));
        $this->assertCount(1, $response['comments']);
    }

    /**
     * @depends testCreateComment
     */
    public function testDeleteComment() {
        $response = static::$client->comments()->delete(static::$testCommentId);
        $this->assertEquals(static::SUCCESS_RESPONSE, $response);

        $response = static::$client->files()->listComments(static::$testFileId);
        $this->assertArrayHasKey('comments', $response);
        $this->assertTrue(is_array($response['comments']));
        $this->assertCount(0, $response['comments']);
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