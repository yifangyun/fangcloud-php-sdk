<?php
/**
 * 群组操作
 */

namespace Fangcloud\Api\Group;

use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;

/**
 * Class YfyGroupClient
 * @package Fangcloud\Api\Group
 */
class YfyGroupClient extends YfyBaseApiClient
{
    const GROUP_LIST_URI = self::API_PREFIX . '/group/list';
    const GROUP_USERS_URI = self::API_PREFIX . '/group/%s/users';

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