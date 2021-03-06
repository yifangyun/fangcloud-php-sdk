<?php
/**
 * 应用信息类
 */
namespace Fangcloud;

use Fangcloud\Exception\YfySdkException;


/**
 * Class YfyAppInfo
 * @package Fangcloud
 */
class YfyAppInfo
{
    /**
     * @var string 用户在亿方云上申请的clientId
     */
    public static $clientId;
    /**
     * @var string 用户在亿方云上申请的clientId对应的secret
     */
    public static $clientSecret;
    /**
     * @var string 用户在亿方云上申请的clientId对应的回调地址
     */
    public static $redirectUri;
    /**
     * @var string 授权服务器地址
     */
    public static $authHost = "https://oauth.fangcloud.com";
    /**
     * @var string 资源服务器地址
     */
    public static $apiHost = "https://open.fangcloud.com";

    const TEST_ENV = "FangcloudTest";


    /**
     * 初始化应用
     * 该方法应当在使用{@see fangcloud\YfyClient} 前被调用
     *
     * @param string $clientId      用户在亿方云上申请的clientId
     * @param string $clientSecret  亿方云上申请的应用的secret
     * @param string $redirectUri   用户在亿方云上申请的clientId对应的回调地址
     */
    public static function init($clientId, $clientSecret, $redirectUri) {
        self::$clientId = $clientId;
        self::$clientSecret = $clientSecret;
        self::$redirectUri = $redirectUri;
        if (getenv(self::TEST_ENV)) {
            self::$authHost = "https://oauth-server.fangcloud.net";
            self::$apiHost = "https://platform.fangcloud.net";
        }
    }

    /**
     * 检测是否正确初始化
     */
    public static function checkInit() {
        if (empty(static::$clientId) || empty(static::$clientSecret) || empty(static::$redirectUri)) {
            throw new YfySdkException('App info should be init at first.');
        }
    }

    /**
     * 重置应用信息
     */
    public static function reset() {
        self::$clientId = null;
        self::$clientSecret = null;
        self::$redirectUri = null;
    }
}