<?php
/**
 * 部门管理操作
 */

namespace Fangcloud\Api\Admin\Department;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;

/**
 * Class YfyJwtDepartmentClient
 * @package Fangcloud\Api\Admin\Department
 */
class YfyJwtDepartmentClient extends YfyBaseApiClient
{
    const DEPARTMENT_INFO_URI = self::API_PREFIX . '/admin/department/%s/info';
    const DEPARTMENT_CREATE_URI = self::API_PREFIX . '/admin/department/create';
    const DEPARTMENT_UPDATE_URI = self::API_PREFIX . '/admin/department/%s/update';
    const DEPARTMENT_ADD_USER_URI = self::API_PREFIX . '/admin/department/%s/add_user';
    const DEPARTMENT_REMOVE_USER_URI = self::API_PREFIX . '/admin/department/%s/remove_user';
    const DEPARTMENT_CHILDREN_URI = self::API_PREFIX . '/admin/department/%s/children';
    const DEPARTMENT_USERS_URI = self::API_PREFIX . '/admin/department/%s/users';

    /**
     * YfyJwtDepartmentClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 获取部门设置信息
     *
     * @param int $departmentId 部门id
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($departmentId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($departmentId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 创建部门
     *
     * @param string $name 部门名称，长度不能超过30个字符
     * @param int $parentId 父部门id
     * @param int $directorId 部门管理员id
     * @param array $specialUsers 特殊成员信息, 格式参考如下
     *
     *      $$specialUsers = array(
     *          array(
     *              'user_id' => 111,
     *              'role' => YfyCollabRole::VIEWER
     *          )
     *      );
     *
     * @param int $spaceTotal 总共空间，单位为GB，默认为20
     * @param bool $hidePhone 是否隐藏部门成员手机号，默认和上级部门保持一致
     * @param bool $disableShare 是否禁用分享，默认和上级部门保持一致
     * @param bool $enableWatermark 是否开启水印预览，默认和上级部门保持一致
     * @param bool $collabAutoAccepted 是否自动接受协作邀请，默认false
     * @param bool $createCommonFolder 是否建立公共资料库，默认false
     * @return mixed
     * @throws YfySdkException
     */
    public function create(
        $name,
        $parentId,
        $directorId = null,
        $specialUsers = null,
        $spaceTotal = null,
        $hidePhone = null,
        $disableShare = null,
        $enableWatermark = null,
        $collabAutoAccepted = null,
        $createCommonFolder = null
    ) {
        $json = [
            'name' => $name,
            'parent_id' => $parentId
        ];

        if (is_int($directorId)) {
            $json['director_id'] = $directorId;
        }
        if (is_array($specialUsers)) {
            $json['special_users'] = $specialUsers;
        }
        if (is_int($spaceTotal)) {
            $json['space_total'] = $spaceTotal;
        }
        if (is_bool($hidePhone)) {
            $json['hide_phone'] = $hidePhone;
        }
        if (is_bool($disableShare)) {
            $json['disable_share'] = $disableShare;
        }
        if (is_bool($enableWatermark)) {
            $json['enable_watermark'] = $enableWatermark;
        }
        if (is_bool($collabAutoAccepted)) {
            $json['collab_auto_accepted'] = $collabAutoAccepted;
        }
        if (is_bool($createCommonFolder)) {
            $json['create_common_folder'] = $createCommonFolder;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_CREATE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 修改部门
     *
     * @param string $name 部门名称，长度不能超过30个字符
     * @param int $departmentId 部门id
     * @param int $directorId 部门管理员id
     * @param array $specialUsers 特殊成员信息, 格式参考如下
     *
     *      $$specialUsers = array(
     *          array(
     *              'user_id' => 111,
     *              'role' => YfyCollabRole::VIEWER
     *          )
     *      );
     *
     * @param int $spaceTotal 总共空间，单位为GB，默认为20
     * @param bool $hidePhone 是否隐藏部门成员手机号，默认和上级部门保持一致
     * @param bool $disableShare 是否禁用分享，默认和上级部门保持一致
     * @param bool $enableWatermark 是否开启水印预览，默认和上级部门保持一致
     * @param bool $collabAutoAccepted 是否自动接受协作邀请，默认false
     * @return mixed
     * @throws YfySdkException
     */
    public function update(
        $departmentId,
        $name,
        $directorId = null,
        $specialUsers = null,
        $spaceTotal = null,
        $hidePhone = null,
        $disableShare = null,
        $enableWatermark = null,
        $collabAutoAccepted = null
    ) {
        $json = [
            'name' => $name,
        ];

        if (is_int($directorId)) {
            $json['director_id'] = $directorId;
        }
        if (is_array($specialUsers)) {
            $json['special_users'] = $specialUsers;
        }
        if (is_int($spaceTotal)) {
            $json['space_total'] = $spaceTotal;
        }
        if (is_bool($hidePhone)) {
            $json['hide_phone'] = $hidePhone;
        }
        if (is_bool($disableShare)) {
            $json['disable_share'] = $disableShare;
        }
        if (is_bool($enableWatermark)) {
            $json['enable_watermark'] = $enableWatermark;
        }
        if (is_bool($collabAutoAccepted)) {
            $json['collab_auto_accepted'] = $collabAutoAccepted;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($departmentId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 添加部门成员
     *
     * @param int $departmentId 部门id
     * @param int $userId 添加的成员用户id
     * @return mixed
     * @throws YfySdkException
     */
    public function addUser($departmentId, $userId) {
        $json = [
            'user_id' => $userId
        ];

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_ADD_USER_URI)
            ->withMethod('POST')
            ->addPathParam($departmentId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 移除部门成员
     *
     * @param int $departmentId 部门id
     * @param int $userId 添加的成员用户id
     * @return mixed
     * @throws YfySdkException
     */
    public function removeUser($departmentId, $userId) {
        $json = [
            'user_id' => $userId
        ];

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_REMOVE_USER_URI)
            ->withMethod('POST')
            ->addPathParam($departmentId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取部门的子部门列表
     *
     * @param int $departmentId 部门id
     * @return mixed
     * @throws YfySdkException
     */
    public function getChildren($departmentId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_CHILDREN_URI)
            ->withMethod('GET')
            ->addPathParam($departmentId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取部门成员详细列表
     *
     * @param int $departmentId 部门id
     * @param string $queryWords 部门成员搜索关键字
     * @param int $pageId 页码
     * @return mixed
     * @throws YfySdkException
     */
    public function getUsers($departmentId, $queryWords = null, $pageId = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_USERS_URI)
            ->withMethod('GET')
            ->addPathParam($departmentId)
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