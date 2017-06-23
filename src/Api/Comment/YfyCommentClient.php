<?php

namespace Fangcloud\Api\Comment;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\YfyRequestBuilder;

class YfyCommentClient extends YfyBaseApiClient
{
    const COMMENT_CREATE_URI = self::API_PREFIX . 'comment/create';
    const COMMENT_DELETE_URI = self::API_PREFIX . 'comment/%s/delete';
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
     * 创建评论
     *
     * @param int $fileId 评论文件id
     * @param string $content 评论文本，长度不能超过1001个字符
     * @return mixed
     * @throws YfySdkException
     */
    public function invite($fileId, $content) {
        $json = [
            'file_id' => $fileId,
            'content' => $content
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::COMMENT_CREATE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除评论
     *
     * @param int $commentId 评论id
     * @return mixed
     * @throws YfySdkException
     */
    public function delete($commentId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::COMMENT_DELETE_URI)
            ->withMethod('POST')
            ->addPathParam($commentId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}