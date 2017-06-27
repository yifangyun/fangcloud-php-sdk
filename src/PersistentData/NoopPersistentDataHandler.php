<?php
/**
 * 一个什么都不处理的持久化数据处理器
 */

namespace Fangcloud\PersistentData;

use Fangcloud\Exception\YfySdkException;

/**
 * 一个什么都不处理的持久化数据处理器
 * 用户不应当调用这里的方法
 *
 * Class NoopPersistentDataHandler
 * @package Fangcloud\PersistentData
 */
class NoopPersistentDataHandler implements PersistentDataHandler
{

    const ERROR_MESSAGE = 'To use default YfySessionPersistentDataHandler, make sure session_start() is at the top of your script.';

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        throw new YfySdkException(static::ERROR_MESSAGE);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        throw new YfySdkException(static::ERROR_MESSAGE);
    }
}