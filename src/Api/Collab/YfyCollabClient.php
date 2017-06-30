<?php
/**
 * 协作操作
 */

namespace Fangcloud\Api\Collab;

use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Constant\YfyCollabRole;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequestBuilder;

/**
 * Class YfyCollabClient
 * @package Fangcloud\Api\Collab
 */
class YfyCollabClient extends YfyBaseApiClient
{
    const COLLAB_INVITE_URI = self::API_PREFIX . '/collab/invite';
    const COLLAB_INFO_URI = self::API_PREFIX . '/collab/%s/info';
    const COLLAB_UPDATE_URI = self::API_PREFIX . '/collab/%s/update';
    const COLLAB_DELETE_URI = self::API_PREFIX . '/collab/%s/delete';

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
     * 邀请协作
     *
     * @param int $folderId 协作文件夹id
     * @param int $userId 邀请用户id
     * @param string $userRole 邀请用户角色, 只能是Fangcloud\Constant\YfyCollabRole中定义的常量
     * @param string $message 邀请信息，长度不能超过140个字符
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyCollabRole
     */
    public function invite($folderId, $userId, $userRole, $message = null) {
        YfyCollabRole::validate($userRole);
        $json = [
            'folder_id' => $folderId,
            'invited_user' => [
                'id' => $userId,
                'role' => $userRole
            ]
        ];
        if (!empty($message)) $json['message'] = $message;
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::COLLAB_INVITE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取协作信息
     *
     * @param int $collabId 协作id
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($collabId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::COLLAB_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($collabId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 更新协作
     *
     * @param int $collabId 协作id
     * @param string $role 用户角色, 只能是Fangcloud\Constant\YfyCollabRole中定义的常量
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyCollabRole
     */
    public function update($collabId, $role) {
        YfyCollabRole::validate($role);
        $json = [
            'role' => $role
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::COLLAB_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($collabId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除协作
     *
     * @param int $collabId 协作id
     * @return mixed
     * @throws YfySdkException
     */
    public function delete($collabId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::COLLAB_DELETE_URI)
            ->withMethod('POST')
            ->addPathParam($collabId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}