<?php
/**
 * 处理授权操作的client
 */
namespace Fangcloud\Authentication;


use Fangcloud\Constant\YfyJwtSubType;
use Fangcloud\Exception\YfyInvalidGrantException;
use Fangcloud\Exception\YfyInvalidStateException;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\Exception\YfyServerException;
use Fangcloud\Exception\YfyUnauthorizedException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\PersistentData\PersistentDataHandler;
use Fangcloud\PersistentData\PersistentDataHandlerFactory;
use Fangcloud\RandomString\RandomStringGenerator;
use Fangcloud\RandomString\RandomStringGeneratorFactory;
use Fangcloud\YfyAppInfo;
use Fangcloud\Http\YfyRequest;
use Fangcloud\Http\YfyRequestBuilder;
use Firebase\JWT\JWT;

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
        $this->persistentDataHandler = $persistentDataHandler ?: PersistentDataHandlerFactory::createPersistentDataHandler();
        $this->randomStringGenerator = $randomStringGenerator ?: RandomStringGeneratorFactory::createRandomStringGenerator();
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
        $rawContent = $rawResponse->getBody()->getContents();
        $body = json_decode($rawContent, true);
        if (is_array($body)) {
            $errors = array_key_exists('errors', $body) ? $body['errors'] : [['code' => 'unknown_error']];
            $requestId = array_key_exists('request_id', $body) ? $body['request_id'] : null;
            switch ($statusCode) {
                case 400:
                    switch (@$errors['code']) {
                        case 'invalid_grant':
                            throw new YfyInvalidGrantException(null, $errors, $requestId);
                            break;
                        default:
                            throw new YfySdkException(null, $errors, $requestId);
                    }
                    break;
                case 401:
                    throw new YfyUnauthorizedException(null, $errors, $requestId);
                    break;
                case 500:
                    throw new YfyServerException(null, $errors, $requestId);
                    break;
                default:
                    throw new YfySdkException(null, $errors, $requestId);
            }
        }
        else {
            if ($statusCode <500) {
                throw new YfySdkException('status code: ' . $statusCode . ' with content ' . $rawContent);
            }
            else {
                throw new YfyServerException('status code: ' . $statusCode . ' with content ' . $rawContent);
            }
        }

    }

    /**
     * 刷新token
     *
     * @param string $refreshToken
     * @return mixed
     * @throws YfySdkException
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
     * @throws YfySdkException
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
     * @param string $username 密码模式用户用户名
     * @param string $password 密码模式用户密码
     * @return mixed
     * @throws YfySdkException
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
     * @throws YfySdkException
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
     * @throws YfySdkException
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
     * @throws YfySdkException
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

    /**
     * 通过jwt模式获取token
     *
     * @param string $subType 授权对象类型，只能是Fangcloud\Constant\YfyJwtSubType中定义的常量
     * @param int $subId 授权对象id
     * @param int $kid 由亿方云下发的代表公钥的唯一id
     * @param string $privateKeyPath 私钥路径
     * @param string $privateKeyContent 私钥文本，该参数存在时优先使用该参数
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     */
    public function getTokenByJwtFlow($subType, $subId, $kid, $privateKeyPath, $privateKeyContent = null) {
        YfyJwtSubType::validate($subType);
        $iat = time();
        $exp = $iat + 45;
        $claims = array(
            'yifangyun_sub_type' => $subType,
            'sub' => $subId,
            'exp' => $exp,
            'iat' => $iat,
            'jti' => $this->randomStringGenerator->getRandomString(16)
        );

        if (is_string($privateKeyContent)) {
            $privateKey = $privateKeyContent;
        }
        else if (file_exists($privateKeyPath)) {
            $privateKey = file_get_contents($privateKeyPath);
        }
        else {
            throw new \InvalidArgumentException($privateKeyPath . ' does not exist!');
        }

        $jwt = JWT::encode($claims, $privateKey, 'RS256', $kid);

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$authHost . self::TOKEN_URI)
            ->withMethod('POST')
            ->withBasicAuth(YfyAppInfo::$clientId, YfyAppInfo::$clientSecret)
            ->addFormParam('grant_type', 'jwt')
            ->addFormParam('assertion', $jwt)
            ->build();
        $response = $this->execute($request);
        return json_decode($response->getBody(), true);
    }


}