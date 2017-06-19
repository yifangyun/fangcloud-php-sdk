<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/12
 * Time: 15:18
 */

namespace Fangcloud\Exception;


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