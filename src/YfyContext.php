<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/5
 * Time: 14:15
 */

namespace Fangcloud;


class YfyContext
{
    /* @var string */
    private $accessToken;
    /* @var string */
    private $refreshToken;
    /** @var  bool */
    private $autoRefresh;

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return boolean
     */
    public function isAutoRefresh()
    {
        return $this->autoRefresh || !empty($this->refreshToken);
    }

    /**
     * @param boolean $autoRefresh
     */
    public function setAutoRefresh($autoRefresh)
    {
        $this->autoRefresh = $autoRefresh;
    }
}