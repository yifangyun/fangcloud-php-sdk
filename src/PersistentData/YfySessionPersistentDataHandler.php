<?php
/**
 * 默认的基于php原生session的持久化数据处理
 */
namespace Fangcloud\PersistentData;

use Fangcloud\Exception\YfySdkException;


/**
 * Class YfySessionPersistentDataHandler
 *
 * @package Fangcloud
 */
class YfySessionPersistentDataHandler implements PersistentDataHandler
{
    /**
     * @var string session前缀
     */
    protected $sessionPrefix = 'YFY_';

    /**
     * YfySessionPersistentDataHandler constructor.
     * @param bool $enableSessionCheck 是否检测session开启状态
     * @throws YfySdkException
     */
    public function __construct($enableSessionCheck = true)
    {
        if ($enableSessionCheck && session_status() !== PHP_SESSION_ACTIVE) {
            throw new YfySdkException(
                'Sessions are not active. Please make sure session_start() is at the top of your script.');
        }
    }

    /**
     * {@inheritdoc}
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($_SESSION[$this->sessionPrefix . $key])) {
            return $_SESSION[$this->sessionPrefix . $key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $_SESSION[$this->sessionPrefix . $key] = $value;
    }
}
