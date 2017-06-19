<?php

require_once ('../../vendor/autoload.php');

class YfyClientFactory
{
    /** @var  \Fangcloud\YfyClient */
    private static $instance;

    public static function getClient() {
        if (empty(static::$instance)) {
            \Fangcloud\YfyAppInfo::init(
                '72ba059d-09a7-4f00-bd15-c4e48a79a155',
                '338ab9bb-decb-4053-9502-dd5fc946457c',
                'http://localhost:8000/callback.php'
            );
            static::$instance = new \Fangcloud\YfyClient(array());
        }
        return static::$instance;
    }
}