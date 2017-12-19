<?php
/**
 * 部门操作
 */

namespace Fangcloud\Api\Department;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;

/**
 * Class YfyDepartmentClient
 * @package Fangcloud\Api\Department
 */
class YfyDepartmentClient extends YfyBaseApiClient
{
    const DEPARTMENT_INFO_URI = self::API_PREFIX . '/department/%s/info';
    const DEPARTMENT_CHILDREN_URI = self::API_PREFIX . '/department/%s/children';
    const DEPARTMENT_USERS_URI = self::API_PREFIX . '/department/%s/users';

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
     * 获取部门信息
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
     * 获取部门的子部门列表
     *
     * @param int $departmentId 部门id
     * @param bool $permissionFilter 是否过滤权限外的部门
     * @return mixed
     * @throws YfySdkException
     */
    public function getChildren($departmentId, $permissionFilter = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::DEPARTMENT_CHILDREN_URI)
            ->withMethod('GET')
            ->addPathParam($departmentId)
            ->withYfyContext($this->yfyContext);
        if (is_bool($permissionFilter)) {
            $builder->addQueryParam('permission_filter', $permissionFilter);
        }
        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取部门成员列表
     *
     * @param int $departmentId 部门id
     * @param string $queryWords 部门成员搜索关键字
     * @param int $pageId 页码
     * @return mixed
     */
    public function getUsers($departmentId, $queryWords = null, $pageId = 0) {
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