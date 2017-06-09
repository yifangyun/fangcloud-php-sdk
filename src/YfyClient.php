<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/1
 * Time: 16:31
 */

namespace Fangcloud;

use Fangcloud\Api\File\YfyFileClient;
use Fangcloud\Api\User\YfyUserClient;
use Fangcloud\HttpClient\YfyHttpClientFactory;
use Fangcloud\Upload\YfyFile;


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

    /** @var  YfyUserClient */
    private $userClient;
    /** @var  YfyFileClient */
    private $fileClient;

    /**
     * YfyClient constructor.
     */
    public function __construct()
    {
    }


    /**
     * 返回用户操作
     *
     * @return YfyUserClient
     */
    public function users() {
        if (!$this->userClient) {
            $this->userClient =  new YfyUserClient($this->yfyContext, $this->httpClient);
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
            $this->fileClient =  new YfyFileClient($this->yfyContext, $this->httpClient);
        }
        return $this->fileClient;
    }
}