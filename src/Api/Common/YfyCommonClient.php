<?php
/**
 * 通用client
 */

namespace Fangcloud\Api\Common;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;

class YfyCommonClient extends YfyBaseApiClient
{
    /**
     * YfyCommonClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 自定义get请求, 返回为array
     *
     * @param string $uri 请求uri, eg. /api/v2/user/info
     * @param array $query query参数
     * @return mixed
     */
    public function get($uri, $query = []) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . $uri)
            ->withMethod('GET')
            ->withQueryParams($query)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 自定义post请求, 返回为array
     *
     * @param string $uri 请求uri, eg. /api/v2/user/info
     * @param array $body 请求body
     * @return mixed
     */
    public function post($uri, $body = []) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . $uri)
            ->withMethod('POST')
            ->withJson($body)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }
}