<?php
/**
 * YfyClient的上下文
 */

namespace Fangcloud;

/**
 * Class YfyContext
 * @package Fangcloud
 */
class YfyContext
{
    /**
     * @var string access token
     */
    private $accessToken;
    /**
     * @var string refresh token
     */
    private $refreshToken;
    /**
     * @var bool 是否自动刷新token
     */
    private $autoRefresh;

    /**
     * 获取access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * 设置access token
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * 获取refresh token
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * 设置refresh token
     *
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * 是否自动刷新token
     *
     * @return boolean
     */
    public function isAutoRefresh()
    {
        return $this->autoRefresh && !empty($this->refreshToken);
    }

    /**
     * 设置是否自动刷新token
     *
     * @param boolean $autoRefresh
     */
    public function setAutoRefresh($autoRefresh)
    {
        $this->autoRefresh = $autoRefresh;
    }
}