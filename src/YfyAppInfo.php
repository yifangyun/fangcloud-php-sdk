<?php

namespace Fangcloud;


/**
 * Class YfyAppInfo
 * @package Fangcloud
 */
class YfyAppInfo
{
    /** @var string */
    public static $clientId;
    /** @var string */
    public static $clientSecret;
    /** @var  string */
    public static $redirectUri;
    /** @var string */
    public static $authHost = "https://oauth.fangcloud.com";
    /** @var string */
    public static $apiHost = "https://open.fangcloud.com";

    const TEST_ENV = "FangcloudTest";


    /**
     * 初始化应用
     * 该方法应当在使用{@see fangcloud\YfyClient} 前被调用
     * @param $clientId string 亿方云上申请的应用的id
     * @param $clientSecret string 亿方云上申请的应用的secret
     * @param $redirectUri
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
}