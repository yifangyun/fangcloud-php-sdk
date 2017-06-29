<?php
/**
 * 请求返回结果的封装
 */

namespace Fangcloud\Http;


use Fangcloud\Download\DownloadFile;
use Fangcloud\Util\HttpUtil;
use Psr\Http\Message\StreamInterface;

/**
 * Class YfyRawResponse
 * @package Fangcloud\Http
 */
class YfyRawResponse
{
    /**
     * @var array 请求返回的header数组
     */
    protected $headers;

    /**
     * @var StreamInterface 请求返回的流
     */
    protected $body;

    /**
     * @var int 请求返回的status code
     */
    protected $httpResponseCode;

    /**
     * YfyRawResponse constructor.
     * @param string|array          $headers        请求返回的header数组
     * @param StreamInterface       $body           请求返回的流
     * @param int                   $httpStatusCode 请求返回的status code
     */
    public function __construct($headers, $body, $httpStatusCode = null)
    {
        if (is_numeric($httpStatusCode)) {
            $this->httpResponseCode = (int)$httpStatusCode;
        }

        if (is_array($headers)) {
            $this->headers = $headers;
        } else {
            $this->setHeadersFromString($headers);
        }

        $this->body = $body;
    }

    /**
     * 获取header数组
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 获取返回的流
     *
     * @return StreamInterface
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * 获取返回的status code
     *
     * @return int
     */
    public function getHttpResponseCode()
    {
        return $this->httpResponseCode;
    }

    /**
     * 从返回的string中提取status code
     *
     * @param string $rawResponseHeader
     */
    private function setHttpResponseCodeFromHeader($rawResponseHeader)
    {
        preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $rawResponseHeader, $match);
        $this->httpResponseCode = (int)$match[1];
    }

    /**
     * 将返回的string解析为header数组
     *
     * @param string $rawHeaders The raw headers from the response.
     */
    protected function setHeadersFromString($rawHeaders)
    {
        if (empty($rawHeaders)) return;
        // Normalize line breaks
        $rawHeaders = str_replace("\r\n", "\n", $rawHeaders);

        // There will be multiple headers if a 301 was followed
        // or a proxy was followed, etc
        $headerCollection = explode("\n\n", trim($rawHeaders));
        // We just want the last response (at the end)
        $rawHeader = array_pop($headerCollection);

        $headerComponents = explode("\n", $rawHeader);
        foreach ($headerComponents as $line) {
            if (strpos($line, ': ') === false) {
                $this->setHttpResponseCodeFromHeader($line);
            } else {
                list($key, $value) = explode(': ', $line, 2);
                $this->headers[$key] = $value;
            }
        }
    }

    /**
     * 将请求返回结果存储为文件(通常用于下载文件)
     *
     * @param string $savePath 存储文件的路径,可以是文件夹,也可以是文件名
     */
    public function saveToFile($savePath) {
        $finalPath = $savePath;
        if (is_dir($savePath)) {
            $finalPath = $finalPath . '/' . HttpUtil::detectFilename($this->headers);
        }
        $fp = fopen($finalPath, 'w');
        while (!$this->body->eof()) {
            fwrite($fp, $this->body->read(1024));
        }
        fclose($fp);
    }

    /**
     * 将请求返回结果以DownloadFile的形式返回给用户
     *
     * @return DownloadFile
     */
    public function createDownloadFile() {
        return new DownloadFile(HttpUtil::detectFilename($this->headers), $this->getBody());
    }
}