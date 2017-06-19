<?php

namespace Fangcloud\Api;


use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfyAuthorizationRequiredException;
use Fangcloud\Exception\YfyInvalidTokenException;
use Fangcloud\Exception\YfyRateLimitException;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Exception\YfyServerException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyContext;
use Fangcloud\YfyRequest;

abstract class YfyBaseApiClient
{
    const API_PREFIX = '/api/v2';
    /* @var YfyContext */
    protected $yfyContext;
    /* @var YfyHttpClient */
    protected $httpClient;
    /** @var  OAuthClient */
    protected $oauthClient;
    /**
     * @var OAuthClient
     */
    private $OAuthClient;

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
        $this->OAuthClient = $oauthClient;
    }

    public function execute(YfyRequest $yfyRequest) {
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


    protected function realExecute(YfyRequest $yfyRequest) {
        $rawResponse = $this->httpClient->send($yfyRequest);
        $statusCode = $rawResponse->getHttpResponseCode();
        if ($statusCode === 200) {
            return $rawResponse;
        }
        elseif ($statusCode >= 400 && $statusCode <= 500) {
            $body = json_decode($rawResponse->getBody(), true);
            $errors = array_key_exists('errors', $body) ? $body['errors'] : null;
            $requestId = array_key_exists('request_id', $body) ? $body['request_id'] : null;
            switch ($statusCode) {
                case 401:
                    switch ($errors[0]['code']) {
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
            throw new YfyServerException('status code: '. $statusCode . ' unknown error');
        }
    }

}