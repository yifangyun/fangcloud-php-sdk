<?php

namespace Fangcloud\Util;

use Fangcloud\Exception\YfySdkException;

class HttpUtil
{
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