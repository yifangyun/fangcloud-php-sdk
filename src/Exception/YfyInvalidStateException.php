<?php
/**
 * 表示state无效的异常
 */

namespace Fangcloud\Exception;

/**
 * Class YfyInvalidStateException
 * @package Fangcloud\Exception
 */
class YfyInvalidStateException extends YfySdkException
{

    /**
     * YfyInvalidStateException constructor.
     * @param string $expectedState
     * @param string $actualState
     */
    public function __construct($expectedState, $actualState)
    {
        parent::__construct("OAuth callback error! Expected state: $expectedState, actual state: $actualState");
    }
}