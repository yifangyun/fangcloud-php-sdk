<?php
/**
 * 所有操作的对外入口
 */
namespace Fangcloud;

use Fangcloud\Api\Admin\YfyAdminClient;
use Fangcloud\Api\Collab\YfyCollabClient;
use Fangcloud\Api\Comment\YfyCommentClient;
use Fangcloud\Api\Common\YfyCommonClient;
use Fangcloud\Api\Department\YfyDepartmentClient;
use Fangcloud\Api\File\YfyFileClient;
use Fangcloud\Api\Folder\YfyFolderClient;
use Fangcloud\Api\Group\YfyGroupClient;
use Fangcloud\Api\Item\YfyItemClient;
use Fangcloud\Api\ShareLink\YfyShareLinkClient;
use Fangcloud\Api\Trash\YfyTrashClient;
use Fangcloud\Api\User\YfyUserClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\HttpClient\YfyHttpClientFactory;
use Fangcloud\PersistentData\PersistentDataHandler;
use Fangcloud\PersistentData\PersistentDataHandlerFactory;
use Fangcloud\RandomString\RandomStringGenerator;
use Fangcloud\RandomString\RandomStringGeneratorFactory;


/**
 * Interface YfyClientInterface
 * @package Fangcloud
 */
class YfyClient
{
    /**
     * @var string SDK版本
     */
    const VERSION = '2.0.0';
    /**
     * @var HttpClient\YfyHttpClient 发送请求用的http client
     */
    private $httpClient;
    /**
     * @var YfyContext 一些上下文信息
     */
    private $yfyContext;
    /**
     * @var PersistentDataHandler 持久化数据处理
     */
    private $persistentDataHandler;
    /**
     * @var RandomStringGenerator 随机字符串生成器
     */
    private $randomStringGenerator;
    /**
     * @var OAuthClient 处理授权操作的client
     */
    private $oauthClient;
    /**
     * @var YfyUserClient 处理用户操作的client
     */
    private $userClient;
    /**
     * @var YfyFileClient 处理文件操作的client
     */
    private $fileClient;
    /**
     * @var YfyFolderClient 处理文件夹操作的client
     */
    private $folderClient;
    /**
     * @var YfyItemClient 处理item操作的client
     */
    private $itemClient;
    /**
     * @var YfyTrashClient 处理回收站操作的client
     */
    private $trashClient;
    /**
     * @var YfyShareLinkClient 处理分享链接操作的client
     */
    private $shareLinkClient;
    /**
     * @var YfyCollabClient 处理协作操作的client
     */
    private $collabClient;
    /**
     * @var YfyCommentClient 处理评论操作的client
     */
    private $commentClient;
    /**
     * @var YfyDepartmentClient 处理部门操作的client
     */
    private $departmentClient;
    /**
     * @var YfyGroupClient 处理群组操作的client
     */
    private $groupClient;
    /**
     * @var YfyAdminClient 处理管理操作的client
     */
    private $adminClient;
    /**
     * @var YfyCommonClient 自定义请求的client
     */
    private $commonClient;

    /**
     * YfyClient constructor.
     * @param array $options 可传入的选项
     * @see YfyClientOptions
     */
    public function __construct(array $options = [])
    {
        YfyAppInfo::checkInit();
        $options = array_merge([
            YfyClientOptions::AUTO_REFRESH => true,
            YfyClientOptions::ACCESS_TOKEN => null,
            YfyClientOptions::REFRESH_TOKEN => null,
            YfyClientOptions::PERSISTENT_DATA_HANDLER => PersistentDataHandlerFactory::createPersistentDataHandler(),
            YfyClientOptions::RANDOM_STRING_GENERATOR => RandomStringGeneratorFactory::createRandomStringGenerator(),
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
     * 获取access token
     */
    public function getAccessToken() {
        return $this->yfyContext->getAccessToken();
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
     * 获取refresh token
     */
    public function getRefreshToken() {
        return $this->yfyContext->getRefreshToken();
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
     * 获取持久化数据处理器
     *
     * @return PersistentDataHandler
     */
    public function getPersistentDataHandler()
    {
        return $this->persistentDataHandler;
    }

    /**
     * 设置持久化数据处理器
     *
     * @param PersistentDataHandler $persistentDataHandler PersistentDataHandler的实例
     */
    public function setPersistentDataHandler($persistentDataHandler)
    {
        $this->persistentDataHandler = $persistentDataHandler;
    }


    /**
     * 获取随机字符串生成器
     *
     * @return RandomStringGenerator
     */
    public function getRandomStringGenerator()
    {
        return $this->randomStringGenerator;
    }

    /**
     * 设置随机字符串生成器
     *
     * @param RandomStringGenerator $randomStringGenerator RandomStringGenerator的实例
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
            $this->oauthClient =  new OAuthClient($this->httpClient, $this->persistentDataHandler, $this->randomStringGenerator);
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

    /**
     * 返回部门操作
     *
     * @return YfyDepartmentClient
     */
    public function departments() {
        if (!$this->departmentClient) {
            $this->departmentClient =  new YfyDepartmentClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->departmentClient;
    }

    /**
     * 返回群组操作
     *
     * @return YfyGroupClient
     */
    public function groups() {
        if (!$this->groupClient) {
            $this->groupClient =  new YfyGroupClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->groupClient;
    }

    /**
     * 返回管理操作
     *
     * @return YfyAdminClient
     */
    public function admin() {
        if (!$this->adminClient) {
            $this->adminClient =  new YfyAdminClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->adminClient;
    }

    /**
     * 返回自定义操作
     *
     * @return YfyCommonClient
     */
    public function custom() {
        if (!$this->commonClient) {
            $this->commonClient =  new YfyCommonClient($this->yfyContext, $this->httpClient, $this->oauth());
        }
        return $this->commonClient;
    }

}