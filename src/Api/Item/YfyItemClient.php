<?php

namespace Fangcloud\Api\Item;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\YfyRequestBuilder;

class YfyItemClient extends YfyBaseApiClient
{
    const ITEM_SEARCH_URI = self::API_PREFIX . 'item/search';

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
     * @param string $type 搜索类型，分为file，folder，all三种，默认为all
     * @param int $pageId 页码
     * @param int $searchInFolder 指定父文件夹
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($queryWords, $type = 'all', $pageId = 0, $searchInFolder = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::ITEM_SEARCH_URI)
            ->withMethod('GET')
            ->withYfyContext($this->yfyContext);
        if (!empty($searchInFolder)) $builder->addQueryParam('search_in_folder', $searchInFolder);
        $builder->addQueryParam('page_id', $pageId);
        $builder->addQueryParam('type', $type);
        $builder->addQueryParam('query_words', $queryWords);
        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}