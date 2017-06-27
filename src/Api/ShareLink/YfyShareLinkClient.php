<?php
/**
 * 分享链接操作
 */
namespace Fangcloud\Api\ShareLink;

use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequestBuilder;

/**
 * Class YfyShareLinkClient
 * @package Fangcloud\Api\ShareLink
 */
class YfyShareLinkClient extends YfyBaseApiClient
{
    const SHARE_LINK_INFO_URI = self::API_PREFIX . 'share_link/%s/info';
    const SHARE_CREATE_URI = self::API_PREFIX . 'share_link/create';
    const SHARE_UPDATE_URI = self::API_PREFIX . 'share_link/%s/update';
    const SHARE_REVOKE_URI = self::API_PREFIX . 'share_link/%s/revoke';
    /**
     * YfyUserClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 获取分享链接信息
     *
     * @param string $uniqueName 分享链接唯一标识
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($uniqueName) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::SHARE_LINK_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($uniqueName)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 创建分享链接
     *
     * @param string $type item类型, 为file或者folder
     * @param int $id item的id
     * @param string $access 分享链接的访问权限, 为public或者company
     * @param string $dueTime 到期时间(格式如:2017-05-30)
     * @param bool $disableDownload 是否不允许下载(默认false)
     * @param bool $passwordProtected 是否有密码(默认false)
     * @param string $password 密码
     * @return mixed
     * @throws YfySdkException
     */
    public function create($type, $id, $access, $dueTime, $disableDownload = false, $passwordProtected = false, $password = null) {
        if ($type === 'file') $id_key = 'file_id';
        elseif ($type === 'folder') $id_key = 'folder_id';
        else throw new \InvalidArgumentException('Type should be file or folder!');
        $json = [
            $id_key => $id,
            'access' => $access,
            'dueTime' => $dueTime,
            'disable_download' => $disableDownload,
            'password_protected' => $passwordProtected,
            'password' => $password
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::SHARE_CREATE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 更新分享链接
     *
     * @param string $uniqueName 分享链接唯一标识
     * @param string $access 分享链接的访问权限, 为public或者company
     * @param string $dueTime 到期时间(格式如:2017-05-30)
     * @param bool $disableDownload 是否不允许下载(默认false)
     * @param bool $passwordProtected 是否有密码(默认false)
     * @param string $password 密码
     * @return mixed
     * @throws YfySdkException
     */
    public function update($uniqueName, $access, $dueTime, $disableDownload = false, $passwordProtected = false, $password = null) {
        $json = [
            'access' => $access,
            'dueTime' => $dueTime,
            'disable_download' => $disableDownload,
            'password_protected' => $passwordProtected,
            'password' => $password
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::SHARE_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($uniqueName)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除分享链接
     *
     * @param string $uniqueName 分享链接唯一标识
     * @return mixed
     * @throws YfySdkException
     */
    public function revoke($uniqueName) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::SHARE_REVOKE_URI)
            ->withMethod('POST')
            ->addPathParam($uniqueName)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }


}