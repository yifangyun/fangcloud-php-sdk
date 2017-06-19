<?php

namespace Fangcloud\Api\User;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Download\DownloadFile;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\YfyRequestBuilder;

/**
 * Class YfyUserClient
 * @package Fangcloud\Api\User
 */
class YfyUserClient extends YfyBaseApiClient
{

    const SELF_INFO_URI = self::API_PREFIX . '/user/info';
    const USER_INFO_URI = self::API_PREFIX . '/user/%s/info';
    const PROFILE_PIC_DOWNLOAD_URI = self::API_PREFIX . '/user/%s/profile_pic_download';
    const UPDATE_SELF_URI = self::API_PREFIX . '/user/update';
    const SEARCH_USER_URI = self::API_PREFIX . '/user/search';

    /**
     * YfyUserClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param \Fangcloud\Authentication\OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 返回用户个人信息
     *
     * @return array
     * @throws YfySdkException
     */
    function getSelf()
    {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::SELF_INFO_URI)
            ->withMethod('GET')
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 返回指定用户信息
     *
     * @param int $userId 指定的用户id
     * @return array
     * @throws YfySdkException
     */
    function getUser($userId)
    {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::USER_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($userId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }


    /**
     * 下载用户头像
     *
     * @param string $userId 用户id
     * @param string $profilePicKey 用户的profile pic key
     * @param string $savePath 保存路径, 可以是文件或者文件夹
     * @return void|DownloadFile
     * @throws YfySdkException
     */
    function downloadProfilePic($userId, $profilePicKey, $savePath)
    {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::PROFILE_PIC_DOWNLOAD_URI)
            ->withMethod('GET')
            ->addPathParam($userId)
            ->addQueryParam('profile_pic_key', $profilePicKey)
            ->withYfyContext($this->yfyContext)
            ->returnStream(true)
            ->build();
        $response =  $this->execute($request);
        if ($savePath) {
            $response->saveToFile($savePath);
        }
        else {
            return $response->createDownloadFile();
        }
    }


    function updateSelf($name)
    {
        $json = array(
            'name' => $name
        );
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::UPDATE_SELF_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }


    function searchUser($queryWords, $pageId = 0)
    {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::USER_INFO_URI)
            ->withMethod('GET')
            ->addQueryParam('query)words', $queryWords)
            ->addQueryParam('page_id', $pageId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}