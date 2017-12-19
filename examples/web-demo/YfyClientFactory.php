<?php

require_once ('../../vendor/autoload.php');

class YfyClientFactory
{
    /** @var  \Fangcloud\YfyClient */
    private static $instance;

    public static function getClient() {
        if (empty(static::$instance)) {
            \Fangcloud\YfyAppInfo::init(
                'e885b1d0-39e4-49eb-be06-16078cf3f613',
                'b366fa56-c50e-4a68-bc12-1044d974d7b8',
                'http://localhost:8000/callback.php'
            );
            static::$instance = new \Fangcloud\YfyClient(array());
        }
        return static::$instance;
    }
}