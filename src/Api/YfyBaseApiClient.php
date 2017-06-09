<?php

namespace Fangcloud\Api;


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

    /**
     * YfyBaseApiClient constructor.
     * @param YfyContext $yfyContext
     * @param $httpClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient)
    {
        $this->yfyContext = $yfyContext;
        $this->httpClient = $httpClient;
    }


    protected function execute(YfyRequest $yfyRequest) {
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
                    throw new YfyInvalidTokenException(null, $errors, $requestId);
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