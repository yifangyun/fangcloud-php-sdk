<?php
/**
 * 请求的封装类
 */

namespace Fangcloud\Http;

/**
 * Class YfyRequest
 * @package Fangcloud
 */
class YfyRequest
{
    /**
     * @var string 请求方法
     */
    protected $method;

    /**
     * @var string 请求url
     */
    protected $url;

    /**
     * @var array 请求header数组
     */
    protected $headers = [];

    /**
     * @var array 请求query参数
     */
    protected $queryParams = [];

    /**
     * @var array 请求表单参数
     */
    protected $formParams = [];

    /**
     * @var array 请求json参数
     */
    protected $json = [];

    /**
     * @var array 通过multipart方式上传的文件
     */
    protected $files = [];

    /**
     * @var bool 是否以流的形式返回
     */
    protected $stream = false;

    /**
     * @var int 连接超时时间
     */
    protected $connectTimeout = 10;

    /**
     * @var int 请求超时时间
     */
    protected $timeout = 10;

    /**
     * 获取连接超时时间
     *
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * 设置连接超时时间
     *
     * @param int $connectTimeout 连接超时时间
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * 获取请求超时时间
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * 设置请求超时时间
     *
     * @param int $timeout 连接超时时间
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 获取请求方法
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * 设置请求方法
     *
     * @param string $method 请求方法
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * 获取请求url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 设置请求url
     *
     * @param string $url 请求url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 获取请求header数组
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 设置请求header数组
     *
     * @param array $headers 请求header数组
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * 获取请求query参数
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * 设置请求query参数
     *
     * @param array $queryParams 请求query参数
     */
    public function setQueryParams($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * 获取请求表单参数
     *
     * @return array
     */
    public function getFormParams()
    {
        return $this->formParams;
    }

    /**
     * 设置请求表单参数
     *
     * @param array $formParams 请求表单参数
     */
    public function setFormParams($formParams)
    {
        $this->formParams = $formParams;
    }

    /**
     * 获取请求json参数
     *
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * 设置请求json参数
     *
     * @param array $json 请求json参数
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

    /**
     * 获取通过multipart方式上传的文件
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * 设置通过multipart方式上传的文件
     *
     * @param array $files 通过multipart方式上传的文件
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * 是否以流的形式返回
     *
     * @return boolean
     */
    public function isStream()
    {
        return $this->stream;
    }

    /**
     * 设置是否以流的形式返回
     *
     * @param boolean $stream 是否以流的形式返回
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * 该请求是否是一个json请求
     *
     * @return bool
     */
    public function isJson() {
        return !empty($this->json);
    }

    /**
     * 该请求是否是一个表单提交请求
     *
     * @return bool
     */
    public function isForm() {
        return !empty($this->formParams) && !$this->isMultipart();
    }

    /**
     * 该请求是否是一个multipart上传请求
     *
     * @return bool
     */
    public function isMultipart() {
        return !empty($this->files);
    }

    /**
     * 该请求是否有query参数
     *
     * @return bool
     */
    public function hasQuery() {
        return !empty($this->queryParams);
    }

    /**
     * 设置这个请求的access token
     *
     * @param $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->headers['Authorization'] = 'Bearer ' . $accessToken;
    }
}