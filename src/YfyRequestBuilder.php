<?php

namespace Fangcloud;

use Fangcloud\Upload\YfyFile;

class YfyRequestBuilder
{
    /* @var string */
    protected $method;
    /* @var string */
    protected $endpoint;
    /* @var array */
    protected $headers = [];
    /* @var array */
    protected $pathParams = [];
    /* @var array */
    protected $queryParams = [];
    /* @var array */
    protected $formParams = [];
    /* @var array */
    protected $json = [];
    /* @var array */
    protected $files = [];
    /* @var int */
    protected $connectTimeout = 10;
    /* @var int */
    protected $timeout = 10;
    /* @var bool */
    protected $stream = false;
    /* @var YfyContext */
    protected $yfyContext;

    /**
     * YfyRequestBuilder constructor.
     * @param YfyContext $yfyContext
     */
    public function __construct(YfyContext $yfyContext = null)
    {
        $this->yfyContext = $yfyContext;
    }


    /**
     * @param $method
     * @return $this
     */
    public function withMethod($method) {
        $this->method = $method;
        return $this;
    }

    public function withEndpoint($endpoint) {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function withHeaders(array $headers) {
        $this->headers = $headers;
        return $this;
    }

    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withPathParams(array $pathParams) {
        $this->pathParams = $pathParams;
        return $this;
    }

    public function addPathParam($param) {
        $this->pathParams[] = $param;
        return $this;
    }

    public function withQueryParams(array $queryParams) {
        $this->queryParams = $queryParams;
        return $this;
    }

    public function addQueryParam($key, $value) {
        $this->queryParams[$key] = $value;
        return $this;
    }

    public function withFormParams(array $formParams) {
        $this->formParams = $formParams;
        return $this;
    }

    public function addFormParam($key, $value) {
        $this->formParams[$key] = $value;
        return $this;
    }

    public function withJson($json) {
        $this->json = $json;
        return $this;
    }

    public function withFiles(array $files) {
        $this->files = $files;
        return $this;
    }

    public function addFile(YfyFile $yfyFile) {
        $this->files[] = $yfyFile;
        return $this;
    }

    public function withConnectTimeout($connectTimeout) {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    public function withTimeout($timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    public function returnStream($stream) {
        $this->stream = $stream;
        return $this;
    }


    /**
     * @param YfyContext $yfyContext
     * @return $this
     */
    public function withYfyContext($yfyContext) {
        $this->yfyContext = $yfyContext;
        return $this;
    }


    private function getMergedHeaders() {
        $defaultHeaders = array(
            'User-Agent' => 'OfficialFangcloudPhpSDK'
        );
        if (!empty($this->yfyContext)) {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->yfyContext->getAccessToken();
        }
        return array_merge($defaultHeaders, $this->headers);
    }


    /**
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
     * @return static
     */
    public static function factory() {
        return new static();
    }
}