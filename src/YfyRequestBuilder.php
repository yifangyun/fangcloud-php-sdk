<?php
/**
 * 请求的封装类
 */
namespace Fangcloud;

use Fangcloud\Upload\YfyFile;

/**
 * Class YfyRequestBuilder
 * @package Fangcloud
 */
class YfyRequestBuilder
{
    /**
     * @var string 请求方法
     */
    protected $method;
    /**
     * @var string 请求路径模板
     */
    protected $endpoint;
    /**
     * @var array 请求header数组
     */
    protected $headers = [];
    /**
     * @var array basic auth参数
     */
    protected $basicAuth = [];
    /**
     * @var array url路径参数
     */
    protected $pathParams = [];
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
     * @var array 通过multipart上传的文件
     */
    protected $files = [];
    /**
     * @var int 连接超时时间
     */
    protected $connectTimeout = 10;
    /**
     * @var int 请求超时时间
     */
    protected $timeout = 10;
    /**
     * @var bool 是否以流的形式返回
     */
    protected $stream = false;
    /**
     * @var YfyContext 请求上下文
     */
    protected $yfyContext;

    /**
     * YfyRequestBuilder constructor.
     * @param YfyContext $yfyContext 请求上下文
     */
    public function __construct(YfyContext $yfyContext = null)
    {
        $this->yfyContext = $yfyContext;
    }


    /**
     * 设置请求方法
     *
     * @param $method 请求方法
     * @return $this
     */
    public function withMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * 设置请求路径模板
     *
     * @param $endpoint 请求路径模板
     * @return $this
     */
    public function withEndpoint($endpoint) {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * 设置请求header数组
     *
     * @param array $headers 请求header数组
     * @return $this
     */
    public function withHeaders(array $headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * 增加一个header
     *
     * @param string $key header的key
     * @param string $value header的value
     * @return $this
     */
    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * 设置basic auth参数
     *
     * @param string $username basic auth中的用户名
     * @param string $password basic auth中的密码
     * @return $this
     */
    public function withBasicAuth($username, $password) {
        $this->basicAuth = [$username, $password];
        return $this;
    }

    /**
     * 设置路径参数
     *
     * @param array $pathParams 路径参数
     * @return $this
     */
    public function withPathParams(array $pathParams) {
        $this->pathParams = $pathParams;
        return $this;
    }

    /**
     * 增加一个路径参数
     *
     * @param string $param 路径参数
     * @return $this
     */
    public function addPathParam($param) {
        $this->pathParams[] = $param;
        return $this;
    }

    /**
     * 设置query参数数组
     *
     * @param array $queryParams query参数数组
     * @return $this
     */
    public function withQueryParams(array $queryParams) {
        $this->queryParams = $queryParams;
        return $this;
    }

    /**
     * 增加一个query参数
     *
     * @param string $key query参数的key
     * @param string $value query参数的value
     * @return $this
     */
    public function addQueryParam($key, $value) {
        $this->queryParams[$key] = $value;
        return $this;
    }

    /**
     * 设置表单参数
     *
     * @param array $formParams 表单参数
     * @return $this
     */
    public function withFormParams(array $formParams) {
        $this->formParams = $formParams;
        return $this;
    }

    /**
     * 增加一个表单参数
     *
     * @param string $key 表单参数的key
     * @param string $value 表单参数的value
     * @return $this
     */
    public function addFormParam($key, $value) {
        $this->formParams[$key] = $value;
        return $this;
    }

    /**
     * 设置json参数
     *
     * @param array $json json参数
     * @return $this
     */
    public function withJson($json) {
        $this->json = $json;
        return $this;
    }

    /**
     * 设置通过multipart方式上传的文件
     *
     * @param array $files
     * @return $this
     */
    public function withFiles(array $files) {
        $this->files = $files;
        return $this;
    }

    /**
     * 增加一个通过multipart方式上传的文件
     *
     * @param YfyFile $yfyFile 上传文件的封装
     * @return $this
     */
    public function addFile(YfyFile $yfyFile) {
        $this->files[] = $yfyFile;
        return $this;
    }

    /**
     * 设置连接超时时间
     *
     * @param int $connectTimeout 连接超时时间
     * @return $this
     */
    public function withConnectTimeout($connectTimeout) {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * 设置请求超时时间
     *
     * @param int $timeout 请求超时时间
     * @return $this
     */
    public function withTimeout($timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * 设置是否以流形式返回
     *
     * @param bool $stream 是否以流形式返回
     * @return $this
     */
    public function returnStream($stream) {
        $this->stream = $stream;
        return $this;
    }


    /**
     * 设置请求上下文
     *
     * @param YfyContext $yfyContext 请求上下文
     * @return $this
     */
    public function withYfyContext($yfyContext) {
        $this->yfyContext = $yfyContext;
        return $this;
    }


    /**
     * 在请求中加入一些额外的header
     * 例如User-Agent, Authorization等
     *
     * @return array
     */
    private function getMergedHeaders() {
        $defaultHeaders = array(
            'User-Agent' => 'OfficialFangcloudPhpSDK'
        );
        if (!empty($this->yfyContext)) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->yfyContext->getAccessToken();
        }
        if (!empty($this->basicAuth)) {
            $basicAuth = $this->basicAuth;
            $defaultHeaders['Authorization'] = 'Basic ' . base64_encode("$basicAuth[0]:$basicAuth[1]");
        }
        return array_merge($defaultHeaders, $this->headers);
    }


    /**
     * 构造request
     *
     * @return YfyRequest
     */
    public function build() {
        $request = new YfyRequest();
        $url = $this->endpoint;
        if (!empty($this->pathParams)) {
            $url = vsprintf($this->endpoint, $this->pathParams);
        }

        $request->setUrl($url);

        $request->setMethod($this->method);
        $request->setHeaders($this->getMergedHeaders());
        $request->setQueryParams($this->queryParams);
        $request->setFormParams($this->formParams);
        $request->setJson($this->json);
        $request->setFiles($this->files);
        $request->setConnectTimeout($this->connectTimeout);
        $request->setTimeout($this->timeout);
        $request->setStream($this->stream);
        return $request;
    }


    /**
     * 创建一个工厂实例
     *
     * @return static
     */
    public static function factory() {
        return new static();
    }
}