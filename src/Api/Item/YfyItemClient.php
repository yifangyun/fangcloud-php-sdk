<?php
/**
 * 文件/文件夹通用操作
 */
namespace Fangcloud\Api\Item;

use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Constant\YfyItemType;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequestBuilder;

/**
 * Class YfyItemClient
 * @package Fangcloud\Api\Item
 */
class YfyItemClient extends YfyBaseApiClient
{
    const ITEM_SEARCH_URI = self::API_PREFIX . '/item/search';

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
     * 搜索
     *
     * @param string $queryWords 搜索关键词
     * @param string $type 搜索类型，只能是Fangcloud\Constant\YfyItemType中定义的常量
     * @param int $pageId 页码
     * @param int $searchInFolder 指定父文件夹
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyItemType
     */
    public function search($queryWords, $type = YfyItemType::ITEM, $pageId = 0, $searchInFolder = null) {
        YfyItemType::validate($type);
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::ITEM_SEARCH_URI)
            ->withMethod('GET')
            ->withYfyContext($this->yfyContext);
        if (!empty($searchInFolder)) {
            $builder->addQueryParam('search_in_folder', $searchInFolder);
        }
        $builder->addQueryParam('page_id', $pageId);
        $builder->addQueryParam('type', $type);
        $builder->addQueryParam('query_words', $queryWords);
        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}