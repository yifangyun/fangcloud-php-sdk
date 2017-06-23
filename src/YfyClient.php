<?php

namespace Fangcloud;

use Fangcloud\Api\Collab\YfyCollabClient;
use Fangcloud\Api\Comment\YfyCommentClient;
use Fangcloud\Api\File\YfyFileClient;
use Fangcloud\Api\Folder\YfyFolderClient;
use Fangcloud\Api\Item\YfyItemClient;
use Fangcloud\Api\ShareLink\YfyShareLinkClient;
use Fangcloud\Api\Trash\YfyTrashClient;
use Fangcloud\Api\User\YfyUserClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\HttpClient\YfyHttpClientFactory;
use Fangcloud\PersistentData\PersistentDataHandler;
use Fangcloud\PersistentData\YfySessionPersistentDataHandler;
use Fangcloud\RandomString\RandomStringGenerator;
use Fangcloud\RandomString\RandomStringGeneratorFactory;


/**
 * Interface YfyClientInterface
 * @package Fangcloud
 */
class YfyClient
{
    /* @var HttpClient\YfyHttpClient */
    private $httpClient;
    /* @var YfyContext */
    private $yfyContext;
    /** @var  PersistentDataHandler */
    private $persistentDataHandler;
    /** @var  RandomStringGenerator */
    private $randomStringGenerator;

    /** @var  OAuthClient */
    private $oauthClient;
    /** @var  YfyUserClient */
    private $userClient;
    /** @var  YfyFileClient */
    private $fileClient;
    /** @var  YfyFolderClient */
    private $folderClient;
    /** @var  YfyItemClient */
    private $itemClient;
    /** @var  YfyTrashClient */
    private $trashClient;
    /** @var  YfyShareLinkClient */
    private $shareLinkClient;
    /** @var  YfyCollabClient */
    private $collabClient;
    /** @var  YfyCommentClient */
    private $commentClient;

    /**
     * YfyClient constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = array_merge([
            'auto_refresh' => true,
            'access_token' => null,
            'refresh_token' => null,
            'persistent_data_handler' => new YfySessionPersistentDataHandler(),
            'random_string_generator' => RandomStringGeneratorFactory::createPseudoRandomStringGenerator(),
        ], $options);
        $this->httpClient = YfyHttpClientFactory::createHttpClient();
        $this->yfyContext = new YfyContext();
        if ($options['access_token']) {
            $this->yfyContext->setAccessToken($options['access_token']);
        }
        if ($options['refresh_token']) {
            $this->yfyContext->setRefreshToken($options['refresh_token']);
        }
        $this->yfyContext->setAutoRefresh($options['auto_refresh']);
    }

    /**
     * 设置access token
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->yfyContext->setAccessToken($accessToken);
    }

    /**
     * 设置refresh token
     *
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken) {
        $this->yfyContext->setRefreshToken($refreshToken);
    }


    /**
     * @return PersistentDataHandler
     */
    public function getPersistentDataHandler()
    {
        return $this->persistentDataHandler;
    }

    /**
     * @param PersistentDataHandler $persistentDataHandler
     */
    public function setPersistentDataHandler($persistentDataHandler)
    {
        $this->persistentDataHandler = $persistentDataHandler;
    }


    /**
     * @return RandomStringGenerator
     */
    public function getRandomStringGenerator()
    {
        return $this->randomStringGenerator;
    }

    /**
     * @param RandomStringGenerator $randomStringGenerator
     */
    public function setRandomStringGenerator($randomStringGenerator)
    {
        $this->randomStringGenerator = $randomStringGenerator;
    }

    /**
     * 返回授权操作
     */
    public function oauth() {
        if (!$this->oauthClient) {
            $this->oauthClient =  new OAuthClient($this->httpClient);
        }
        return $this->oauthClient;
    }

    /**
     * 返回用户操作
     *
     * @return YfyUserClient
     */
    public function users() {
        if (!$this->userClient) {
            $this->userClient =  new YfyUserClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->userClient;
    }

    /**
     * 返回文件操作
     *
     * @return YfyFileClient
     */
    public function files() {
        if (!$this->fileClient) {
            $this->fileClient =  new YfyFileClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->fileClient;
    }

    /**
     * 返回文件夹操作
     *
     * @return YfyFolderClient
     */
    public function folders() {
        if (!$this->folderClient) {
            $this->folderClient =  new YfyFolderClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->folderClient;
    }

    /**
     * 返回文件/文件夹通用操作
     *
     * @return YfyItemClient
     */
    public function items() {
        if (!$this->itemClient) {
            $this->itemClient =  new YfyItemClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->itemClient;
    }

    /**
     * 返回分享链接操作
     *
     * @return YfyShareLinkClient
     */
    public function shareLinks() {
        if (!$this->shareLinkClient) {
            $this->shareLinkClient =  new YfyShareLinkClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->shareLinkClient;
    }

    /**
     * 返回回收站操作
     *
     * @return YfyTrashClient
     */
    public function trash() {
        if (!$this->trashClient) {
            $this->trashClient =  new YfyTrashClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->trashClient;
    }

    /**
     * 返回协作操作
     *
     * @return YfyCollabClient
     */
    public function collabs() {
        if (!$this->collabClient) {
            $this->collabClient =  new YfyCollabClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->collabClient;
    }

    /**
     * 返回评论操作
     *
     * @return YfyCommentClient
     */
    public function comments() {
        if (!$this->commentClient) {
            $this->commentClient =  new YfyCommentClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->commentClient;
    }


}