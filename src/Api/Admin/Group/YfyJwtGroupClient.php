<?php
/**
 * 群组管理操作
 */

namespace Fangcloud\Api\Admin\Group;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;

/**
 * Class YfyJwtGroupClient
 * @package Fangcloud\Api\Admin\Group
 */
class YfyJwtGroupClient extends YfyBaseApiClient
{
    const GROUP_INFO_URI = self::API_PREFIX . '/admin/group/%s/info';
    const GROUP_CREATE_URI = self::API_PREFIX . '/admin/group/create';
    const GROUP_UPDATE_URI = self::API_PREFIX . '/admin/group/%s/update';
    const GROUP_DELETE_URI = self::API_PREFIX . '/admin/group/%s/delete';
    const GROUP_ADD_USER_URI = self::API_PREFIX . '/admin/group/%s/add_user';
    const GROUP_REMOVE_USER_URI = self::API_PREFIX . '/admin/group/%s/remove_user';
    const GROUP_LIST_URI = self::API_PREFIX . '/admin/group/list';
    const GROUP_USERS_URI = self::API_PREFIX . '/admin/group/%s/users';


    /**
     * YfyJwtGroupClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 获取群组设置信息
     *
     * @param int $groupId 部门id
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($groupId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($groupId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 创建群组
     *
     * @param string $name 群组名称，长度不能超过30个字符
     * @param int $adminUserId 群组管理员id，默认没有
     * @param string $description 群组描述
     * @param bool $visible 是否可见，默认false
     * @param bool $collabAutoAccepted 是否自动接受协作邀请，默认false，当群组管理员为空时默认为true
     * @return mixed
     */
    public function create($name, $adminUserId = null, $description = null, $visible = null, $collabAutoAccepted = null) {
        $json = [
            'name' => $name
        ];

        if (is_int($adminUserId)) {
            $json['admin_user_id'] = $adminUserId;
        }
        if (is_string($description)) {
            $json['description'] = $description;
        }
        if (is_bool($visible)) {
            $json['visible'] = $visible;
        }
        if (is_bool($collabAutoAccepted)) {
            $json['collabAutoAccepted'] = $collabAutoAccepted;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_CREATE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 修改群组
     *
     * @param int $groupId 群组id
     * @param string $name 群组名称，长度不能超过30个字符
     * @param int $adminUserId 群组管理员id，默认没有
     * @param string $description 群组描述
     * @param bool $visible 是否可见，默认false
     * @param bool $collabAutoAccepted 是否自动接受协作邀请，默认false，当群组管理员为空时默认为true
     * @return mixed
     */
    public function update($groupId, $name, $adminUserId = null, $description = null, $visible = null, $collabAutoAccepted = null) {
        $json = [
            'name' => $name
        ];

        if (is_int($adminUserId)) {
            $json['admin_user_id'] = $adminUserId;
        }
        if (is_string($description)) {
            $json['description'] = $description;
        }
        if (is_bool($visible)) {
            $json['visible'] = $visible;
        }
        if (is_bool($collabAutoAccepted)) {
            $json['collabAutoAccepted'] = $collabAutoAccepted;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($groupId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除群组
     *
     * @param int $groupId 群组id
     * @return mixed
     * @throws YfySdkException
     */
    public function delete($groupId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_DELETE_URI)
            ->withMethod('POST')
            ->addPathParam($groupId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 添加群组成员
     *
     * @param int $groupId 群组id
     * @param int $userId 添加的成员用户id
     * @return mixed
     * @throws YfySdkException
     */
    public function addUser($groupId, $userId) {
        $json = [
            'user_id' => $userId
        ];

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_ADD_USER_URI)
            ->withMethod('POST')
            ->addPathParam($groupId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 移除群组成员
     *
     * @param int $groupId 群组id
     * @param int $userId 添加的成员用户id
     * @return mixed
     * @throws YfySdkException
     */
    public function removeUser($groupId, $userId) {
        $json = [
            'user_id' => $userId
        ];

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_REMOVE_USER_URI)
            ->withMethod('POST')
            ->addPathParam($groupId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取群组的列表
     *
     * @param string $queryWords 查询关键字
     * @return mixed
     * @throws YfySdkException
     */
    public function listGroups($queryWords = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_LIST_URI)
            ->withMethod('GET')
            ->withYfyContext($this->yfyContext);
        if (is_string($queryWords)) {
            $builder->addQueryParam('query_words', $queryWords);
        }
        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }


    /**
     * 获取群组成员列表
     *
     * @param int $groupId 群组id
     * @param string $queryWords 群组成员搜索关键字
     * @param int $pageId 页码
     * @return mixed
     * @throws YfySdkException
     */
    public function getUsers($groupId, $queryWords = null, $pageId = 0) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::GROUP_USERS_URI)
            ->withMethod('GET')
            ->addPathParam($groupId)
            ->withYfyContext($this->yfyContext);
        if (is_string($queryWords)) {
            $builder->addQueryParam('query_words', $queryWords);
        }
        if (is_int($pageId)) {
            $builder->addQueryParam('page_id', $pageId);
        }
        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}