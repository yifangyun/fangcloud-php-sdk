<?php
/**
 * 管理操作
 */

namespace Fangcloud\Api\Admin;


use Fangcloud\Api\Admin\Department\YfyJwtDepartmentClient;
use Fangcloud\Api\Admin\Group\YfyJwtGroupClient;
use Fangcloud\Api\Admin\User\YfyJwtUserClient;
use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyContext;

/**
 * Class YfyAdminClient
 * @package Fangcloud\Api\Admin
 */
class YfyAdminClient extends YfyBaseApiClient
{
    /**
     * @var YfyJwtDepartmentClient 部门管理操作
     */
    private $departmentClient;

    /**
     * @var YfyJwtGroupClient 群组管理操作
     */
    private $groupClient;

    /**
     * @var YfyJwtUserClient 用户管理操作
     */
    private $userClient;

    /**
     * YfyAdminClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 返回部门管理操作
     *
     * @return YfyJwtDepartmentClient
     */
    public function departments() {
        if (!$this->departmentClient) {
            $this->departmentClient =  new YfyJwtDepartmentClient($this->yfyContext, $this->httpClient, $this->oauthClient);
        }
        return $this->departmentClient;
    }

    /**
     * 返回群组管理操作
     *
     * @return YfyJwtGroupClient
     */
    public function groups() {
        if (!$this->groupClient) {
            $this->groupClient =  new YfyJwtGroupClient($this->yfyContext, $this->httpClient, $this->oauthClient);
        }
        return $this->groupClient;
    }

    /**
     * 返回用户管理操作
     *
     * @return YfyJwtUserClient
     */
    public function users() {
        if (!$this->userClient) {
            $this->userClient =  new YfyJwtUserClient($this->yfyContext, $this->httpClient, $this->oauthClient);
        }
        return $this->userClient;
    }

}