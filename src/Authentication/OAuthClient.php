<?php
/**
 * 处理授权操作的client
 */
namespace Fangcloud\Authentication;


use Fangcloud\Exception\YfyInvalidStateException;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Exception\YfyServerException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\PersistentData\PersistentDataHandler;
use Fangcloud\PersistentData\YfySessionPersistentDataHandler;
use Fangcloud\RandomString\RandomStringGenerator;
use Fangcloud\RandomString\RandomStringGeneratorFactory;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyRequest;
use Fangcloud\YfyRequestBuilder;

/**
 * Class OAuthClient
 * @package Fangcloud\Authentication
 */
class OAuthClient
{
    const AUTHORIZATION_URI = '/oauth/authorize';
    const TOKEN_URI = '/oauth/token';
    const REVOKE_URI = '/oauth/token/revoke';

    const PERSISTENT_DATA_STATE_KEY = 'yfy_oauth_state';

    /**
     * @var YfyHttpClient 发送请求用的http client
     */
    private $httpClient;
    /**
     * @var PersistentDataHandler 持久化数据处理
     */
    private $persistentDataHandler;
    /**
     * @var RandomStringGenerator 随机字符串生成器
     */
    private $randomStringGenerator;

    /**
     * OAuthClient constructor.
     * @param YfyHttpClient $httpClient
     * @param PersistentDataHandler $persistentDataHandler
     * @param RandomStringGenerator $randomStringGenerator
     */
    public function __construct(YfyHttpClient $httpClient, PersistentDataHandler $persistentDataHandler = null, RandomStringGenerator $randomStringGenerator = null)
    {
        $this->httpClient = $httpClient;
        $this->persistentDataHandler = $persistentDataHandler ?: new YfySessionPersistentDataHandler();
        $this->randomStringGenerator = $randomStringGenerator ?: RandomStringGeneratorFactory::createPseudoRandomStringGenerator();
    }

    /**
     * 执行所有授权请求, 并且进行错误处理
     *
     * @param YfyRequest $yfyRequest
     * @return \Fangcloud\Http\YfyRawResponse
     * @throws YfySdkException
     */
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
                    throw new YfySdkException(null, $errors, $requestId);
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

    /**
     * 刷新token
     *
     * @param string $refreshToken
     * @return mixed
     */
    public function refreshToken($refreshToken) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$authHost . self::TOKEN_URI)
            ->withMethod('POST')
            ->withBasicAuth(YfyAppInfo::$clientId, YfyAppInfo::$clientSecret)
            ->addFormParam('grant_type', 'refresh_token')
            ->addFormParam('refresh_token', $refreshToken)
            ->build();
        $response = $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 通过授权码模式获取token
     *
     * @param $code
     * @return mixed
     */
    private function getTokenByAuthorizationCodeFlow($code) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$authHost . self::TOKEN_URI)
            ->withMethod('POST')
            ->withBasicAuth(YfyAppInfo::$clientId, YfyAppInfo::$clientSecret)
            ->addFormParam('grant_type', 'authorization_code')
            ->addFormParam('code', $code)
            ->addFormParam('redirect_uri', YfyAppInfo::$redirectUri)
            ->build();
        $response = $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 通过密码模式获取token
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public function getTokenByPasswordFlow($username, $password) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$authHost . self::TOKEN_URI)
            ->withMethod('POST')
            ->withBasicAuth(YfyAppInfo::$clientId, YfyAppInfo::$clientSecret)
            ->addFormParam('grant_type', 'password')
            ->addFormParam('username', $username)
            ->addFormParam('password', $password)
            ->build();
        $response = $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 取消一个token的授权(对应的refresh token也会失效)
     *
     * @param $token
     */
    public function revokeToken($token) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$authHost . self::REVOKE_URI)
            ->withMethod('POST')
            ->withBasicAuth(YfyAppInfo::$clientId, YfyAppInfo::$clientSecret)
            ->addFormParam('token', $token)
            ->build();
        $this->execute($request);
    }

    /**
     * 获取授权url
     * @param string|null $state
     * @return string
     */
    public function getAuthorizationUrl($state = null) {
        if (empty($state)) {
            $state = $this->randomStringGenerator->getRandomString(10);
        }
        // save state to persistent data
        if (!empty($state)) {
            $this->persistentDataHandler->set(static::PERSISTENT_DATA_STATE_KEY, $state);
        }

        $query = array(
            'response_type' => 'code',
            'client_id' => YfyAppInfo::$clientId,
            'redirect_uri' => YfyAppInfo::$redirectUri,
            'state' => $state
        );
        return YfyAppInfo::$authHost . self::AUTHORIZATION_URI . '?' . http_build_query($query);
    }


    /**
     * 结束授权码流程
     *
     * @param string|null $code
     * @param string|null $state
     * @return mixed
     */
    public function finishAuthorizationCodeFlow($code = null, $state = null) {
        $code = $code ?: $_GET['code'];
        $state = $state ?: $_GET['state'];
        $this->validateState($state);
        return $this->getTokenByAuthorizationCodeFlow($code);
    }

    /**
     * 校验state参数
     *
     * @param string $state
     * @throws YfyInvalidStateException
     */
    private function validateState($state) {
        if (empty($state)) return;
        $expected = $this->persistentDataHandler->get(static::PERSISTENT_DATA_STATE_KEY);
        if ($expected !== $state) {
            throw new YfyInvalidStateException($expected, $state);
        }
    }

}