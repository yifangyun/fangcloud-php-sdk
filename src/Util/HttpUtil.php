<?php
/**
 * Http请求的工具方法类
 */
namespace Fangcloud\Util;

use Fangcloud\Exception\YfySdkException;

/**
 * Class HttpUtil
 * @package Fangcloud\Util
 */
class HttpUtil
{
    /**
     * 从response headers中检测文件名
     *
     * @param array $headers response的header数组
     * @return string
     * @throws YfySdkException
     */
    public static function detectFilename(array $headers)
    {
        if (isset($headers['Content-Disposition'])) {
            $params = explode(';', $headers['Content-Disposition']);
            foreach ($params as $param) {
                $matches = [];
                if (preg_match('/filename="?([^"]+)"?$/', $param, $matches)) {
                    return $matches[1];
                }
            }
        }
        throw new YfySdkException('Can not detect filename from headers.');
    }
}