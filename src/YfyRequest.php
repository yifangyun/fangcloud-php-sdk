<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/6
 * Time: 11:20
 */

namespace Fangcloud;


class YfyRequest
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $queryParams = [];

    /**
     * @var array
     */
    protected $formParams = [];

    /**
     * @var array
     */
    protected $json = [];

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var bool
     */
    protected $stream = false;

    /**
     * @var int
     */
    protected $connectTimeout = 10;

    /**
     * @var int
     */
    protected $timeout = 10;

    /**
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param array $queryParams
     */
    public function setQueryParams($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * @return array
     */
    public function getFormParams()
    {
        return $this->formParams;
    }

    /**
     * @param array $formParams
     */
    public function setFormParams($formParams)
    {
        $this->formParams = $formParams;
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param array $json
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return boolean
     */
    public function isStream()
    {
        return $this->stream;
    }

    /**
     * @param boolean $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }


    public function isJson() {
        return !empty($this->json);
    }

    public function isForm() {
        return !empty($this->formParams) && !$this->isMultipart();
    }

    public function isMultipart() {
        return !empty($this->files);
    }

    public function hasQuery() {
        return !empty($this->queryParams);
    }

    public function setAccessToken($accessToken) {
        $this->headers['Authorization'] = 'Bearer ' . $accessToken;
    }
}