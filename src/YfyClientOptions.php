<?php
/**
 * 构造YfyClient的参数
 */

namespace Fangcloud;

/**
 * Class YfyClientOptions
 * @package Fangcloud
 */
final class YfyClientOptions
{
    /**
     * access_token: (string) 构造时可以直接设置一个access token。也可以在
     * 构造完成后通过setAccessToken方法设置
     */
    const ACCESS_TOKEN = 'access_token';

    /**
     * refresh_token: (string) 构造时可以直接设置一个refresh token。也可以在
     * setRefreshToken
     */
    const REFRESH_TOKEN = 'refresh_token';

    /**
     * auto_refresh: (bool) 当access_token过期时是否自动进行刷新
     */
    const AUTO_REFRESH = 'auto_refresh';

    /**
     * persistent_data_handler: (Fangcloud\PersistentData\PersistentDataHandler)
     * 用户可传入自己实现的PersistentDataHandler, 主要用于保存OAuth2.0协议中的state参数
     */
    const PERSISTENT_DATA_HANDLER = 'persistent_data_handler';

    /**
     * random_string_generator: (Fangcloud\RandomString\RandomStringGenerator)
     * 用户可传入自己实现的RandomStringGenerator, 主要用于生成OAuth2.0协议中的state参数
     */
    const RANDOM_STRING_GENERATOR = 'random_string_generator';
}