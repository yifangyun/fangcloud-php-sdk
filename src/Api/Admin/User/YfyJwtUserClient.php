<?php
/**
 * 用户管理操作
 */

namespace Fangcloud\Api\Admin\User;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Constant\YfyIdentifierType;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;

/**
 * Class YfyJwtUserClient
 * @package Fangcloud\Api\Admin\User
 */
class YfyJwtUserClient extends YfyBaseApiClient
{
    const USER_INFO_URI = self::API_PREFIX . '/admin/user/%s/info';
    const USER_CREATE_URI = self::API_PREFIX . '/admin/user/create';
    const USER_UPDATE_URI = self::API_PREFIX . '/admin/user/%s/update';
    const USER_GET_LOGIN_URL_URI = self::API_PREFIX . '/admin/user/get_login_url';

    /**
     * YfyJwtUserClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 获取用户设置信息
     *
     * @param int $userId 用户id
     * @return mixed
     * @throws YfySdkException
     */
    public function getUser($userId)
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
     * 创建用户
     *
     * @param string $identifierType 凭据类型，必须是Fangcloud\Constant\YfyIdentifierType中定义的常量
     * @param string $identifier 凭据
     * @param int $storageId 用户存储id，非多存储不需要关心
     * @param int $spaceTotal 用户空间大小，-1为无限，单位为GB
     * @param int $hidePhone 是否隐藏手机号，默认为false
     * @param int $disableDownload 是否禁用下载，默认为false
     * @param bool $forceActive 是否强制激活，默认为false
     * @param string $password 当force_active为true时，必须提供并且长度在6-32位之间
     * @return mixed
     */
    public function create($identifierType, $identifier, $storageId = null, $spaceTotal = null, $hidePhone = null, $disableDownload = null, $forceActive = null, $password = null) {
        YfyIdentifierType::validate($identifierType);

        $json = [
            $identifierType => $identifier
        ];
        if (is_int($storageId)) {
            $json['storage_id'] = $storageId;
        }
        if (is_int($spaceTotal)) {
            $json['space_total'] = $spaceTotal;
        }
        if (is_bool($hidePhone)) {
            $json['hide_phone'] = $hidePhone;
        }
        if (is_bool($disableDownload)) {
            $json['disable_download'] = $disableDownload;
        }
        if (is_bool($forceActive)) {
            $json['force_active'] = $forceActive;
            $json['password'] = $password;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::USER_CREATE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 修改用户
     *
     * @param int $userId 用户id
     * @param string $name 名字
     * @param int $storageId 用户存储id，非多存储不需要关心
     * @param int $spaceTotal 用户空间大小，-1为无限，单位为GB
     * @param int $hidePhone 是否隐藏手机号，默认为false
     * @param int $disableDownload 是否禁用下载，默认为false
     * @return mixed
     */
    public function update($userId, $name = null, $storageId = null, $spaceTotal = null, $hidePhone = null, $disableDownload = null) {
        $json = [];

        if (is_string($name)) {
            $json['name'] = $name;
        }
        if (is_int($storageId)) {
            $json['storage_id'] = $storageId;
        }
        if (is_int($spaceTotal)) {
            $json['space_total'] = $spaceTotal;
        }
        if (is_bool($hidePhone)) {
            $json['hide_phone'] = $hidePhone;
        }
        if (is_bool($disableDownload)) {
            $json['disable_download'] = $disableDownload;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::USER_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($userId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取指定用户登录链接
     *
     * @param string $identifierType 凭据类型，必须是Fangcloud\Constant\YfyIdentifierType中定义的常量
     * @param string $identifier 凭据
     * @return mixed
     * @throws YfySdkException
     */
    public function getLoginUrl($identifierType, $identifier) {
        YfyIdentifierType::validate($identifierType);

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::USER_GET_LOGIN_URL_URI)
            ->withMethod('GET')
            ->addQueryParam('type', $identifierType)
            ->addQueryParam('identifier', $identifier)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}