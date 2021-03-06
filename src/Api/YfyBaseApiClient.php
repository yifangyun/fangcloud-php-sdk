<?php
/**
 * 所有api client的基类
 * 在这里处理请求的发送以及统一的错误处理(包括重试机制)
 */
namespace Fangcloud\Api;


use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfyAuthorizationRequiredException;
use Fangcloud\Exception\YfyInvalidTokenException;
use Fangcloud\Exception\YfyRateLimitException;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Exception\YfyServerException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequest;

/**
 * Class YfyBaseApiClient
 * @package Fangcloud\Api
 */
abstract class YfyBaseApiClient
{
    const API_PREFIX = '/api/v2';
    /**
     * @var YfyContext 请求上下文
     */
    protected $yfyContext;

    /**
     * @var YfyHttpClient 执行请求的HttpClient
     */
    protected $httpClient;

    /**
     * @var OAuthClient refresh token时使用的oauth client
     */
    protected $oauthClient;

    /**
     * YfyBaseApiClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        $this->yfyContext = $yfyContext;
        $this->httpClient = $httpClient;
        $this->oauthClient = $oauthClient;
    }

    /**
     * 执行请求, 调用realExecute, 封装了重试的逻辑
     *
     * @param YfyRequest $yfyRequest
     * @return \Fangcloud\Http\YfyRawResponse
     * @throws YfySdkException
     */
    protected function execute(YfyRequest $yfyRequest) {
        $maxRetries = 1;
        $retires = 0;
        while (true) {
            try {
                return $this->realExecute($yfyRequest);
            }
            catch (YfyInvalidTokenException $e) {
                if (!$this->yfyContext->isAutoRefresh() || $retires >= $maxRetries) {
                    throw $e;
                }
                $tokenResponse = $this->oauthClient->refreshToken($this->yfyContext->getRefreshToken());
                $this->yfyContext->setAccessToken($tokenResponse['access_token']);
                $this->yfyContext->setRefreshToken($tokenResponse['refresh_token']);
                $yfyRequest->setAccessToken($tokenResponse['access_token']);
                $retires++;
            }
        }
    }

    /**
     * 真正执行请求的函数
     * 会进行所有错误处理
     *
     * @param YfyRequest $yfyRequest
     * @return \Fangcloud\Http\YfyRawResponse
     * @throws YfyAuthorizationRequiredException
     * @throws YfyInvalidTokenException
     * @throws YfyRateLimitException
     * @throws YfySdkException
     * @throws YfyServerException
     */
    protected function realExecute(YfyRequest $yfyRequest) {
        $rawResponse = $this->httpClient->send($yfyRequest);
        $statusCode = $rawResponse->getHttpResponseCode();
        if ($statusCode === 200) {
            return $rawResponse;
        }
        $rawContent = $rawResponse->getBody()->getContents();
        $body = json_decode($rawContent, true);
        if (is_array($body)) {
            $errors = array_key_exists('errors', $body) ? $body['errors'] : [['code' => 'unknown_error']];
            $requestId = array_key_exists('request_id', $body) ? $body['request_id'] : null;
            switch ($statusCode) {
                case 401:
                    switch (@$errors[0]['code']) {
                        // without token
                        case 'unauthorized':
                            throw new YfyAuthorizationRequiredException(null, $errors, $requestId);
                            break;
                        // with invalid token
                        case 'invalid_token':
                            throw new YfyInvalidTokenException(null, $errors, $requestId);
                            break;
                    }
                    break;
                case 429:
                    throw new YfyRateLimitException(null, $errors, $requestId);
                    break;
                case 500:
                    throw new YfyServerException(null, $errors, $requestId);
                    break;
                default:
                    throw new YfySdkException(null, $errors, $requestId);
            }
        }
        else {
            if ($statusCode < 500) {
                throw new YfySdkException('status code: ' . $statusCode . ' with content ' . $rawContent);
            }
            else {
                throw new YfyServerException('status code: ' . $statusCode . ' with content ' . $rawContent);
            }
        }
    }

}