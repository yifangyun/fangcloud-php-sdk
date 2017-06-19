<?php

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
     * @var string Prefix to use for session variables.
     */
    protected $sessionPrefix = 'YFY_';

    /**
     * Init the session handler.
     *
     * @param boolean $enableSessionCheck
     *
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
     * @inheritdoc
     */
    public function get($key)
    {
        if (isset($_SESSION[$this->sessionPrefix . $key])) {
            return $_SESSION[$this->sessionPrefix . $key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $_SESSION[$this->sessionPrefix . $key] = $value;
    }
}
