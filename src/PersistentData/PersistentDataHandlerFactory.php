<?php
/**
 * 持久化数据处理器工厂
 */

namespace Fangcloud\PersistentData;

/**
 * Class PersistentDataHandlerFactory
 * @package Fangcloud\PersistentData
 */
class PersistentDataHandlerFactory
{

    /**
     * PersistentDataHandlerFactory constructor.
     */
    public function __construct()
    {
    }

    /**
     * 创建一个默认的持久化数据处理器
     *
     * @param $handler
     * @return PersistentDataHandler
     */
    public static function createPersistentDataHandler($handler = null) {
        if ($handler instanceof PersistentDataHandler) {
            return $handler;
        }

        if (is_null($handler)) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                return new NoopPersistentDataHandler();
            } else {
                return new YfySessionPersistentDataHandler();
            }
        }

        throw new \InvalidArgumentException('handler should be an instance of PersistentDataHandler');
    }
}