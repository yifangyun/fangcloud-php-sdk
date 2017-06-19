<?php

namespace Fangcloud;

use Fangcloud\Api\File\YfyFileClient;
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


}