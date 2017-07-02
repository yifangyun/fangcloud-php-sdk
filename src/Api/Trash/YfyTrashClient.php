<?php
/**
 * 回收站操作
 */
namespace Fangcloud\Api\Trash;

use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Constant\YfyItemType;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequestBuilder;

/**
 * Class YfyTrashClient
 * @package Fangcloud\Api\Trash
 */
class YfyTrashClient extends YfyBaseApiClient
{
    const TRASH_LIST_URI = self::API_PREFIX . '/trash/list';
    const TRASH_CLEAR_URI = self::API_PREFIX . '/trash/clear';
    const TRASH_RESTORE_ALL_URI = self::API_PREFIX . '/trash/restore_all';

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
     * 获取回收站中的文件和文件夹列表
     *
     * @param int $pageId 页码, 默认为0
     * @param int $pageCapacity 页容量, 默认为20
     * @param string $type item类型, 只能是Fangcloud\Constant\YfyItemType中定义的常量
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyItemType
     */
    public function listItems($pageId = 0, $pageCapacity = 20, $type = YfyItemType::ITEM) {
        YfyItemType::validate($type);
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::TRASH_LIST_URI)
            ->withMethod('GET')
            ->addQueryParam('page_id', $pageId)
            ->addQueryParam('page_capacity', $pageCapacity)
            ->addQueryParam('type', $type)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 清空回收站
     *
     * @param string $type item类型, 只能是Fangcloud\Constant\YfyItemType中定义的常量
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyItemType
     */
    public function clear($type = YfyItemType::ITEM) {
        YfyItemType::validate($type);
        $json = [
            'type' => $type
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::TRASH_CLEAR_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 从回收站恢复所有文件/文件夹
     *
     * @param string $type item类型, 只能是Fangcloud\Constant\YfyItemType中定义的常量
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyItemType
     */
    public function restoreAll($type = YfyItemType::ITEM) {
        YfyItemType::validate($type);
        $json = [
            'type' => $type
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::TRASH_RESTORE_ALL_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}