<?php

namespace Fangcloud\Api\File;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\Upload\YfyFile;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\YfyRequestBuilder;
use Psr\Http\Message\StreamInterface;

class YfyFileClient extends YfyBaseApiClient
{

    const FILE_UPLOAD_URI = self::API_PREFIX . '/file/upload';
    const FILE_DOWNLOAD_URI = self::API_PREFIX . '/file/%s/download';

    /**
     * YfyUserClient constructor.
     * @param YfyContext $yfyContext
     * @param YfyHttpClient $httpClient
     * @param OAuthClient $oauthClient
     */
    public function __construct(YfyContext $yfyContext, YfyHttpClient $httpClient, OAuthClient $oauthClient)
    {
        parent::__construct($yfyContext, $httpClient, $oauthClient);
    }

    /**
     * 上传文件
     *
     * @param $parentId
     * @param $name
     * @param resource|StreamInterface|string $resource
     */
    public function uploadFile($parentId, $name, $resource) {
        // presign
        $json = array(
            'parent_id' => $parentId,
            'name' => $name,
            'upload_type' => 'api'
        );

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_UPLOAD_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        $responseBody = json_decode($response->getBody(), true);
        $uploadLink = $responseBody['presign_url'];

        // multipart upload
        if (is_string($resource)) {
            $uploadFile = new YfyFile('file', $name, \GuzzleHttp\Psr7\stream_for(fopen($resource, 'r+')));
        }
        else {
            $uploadFile = new YfyFile('file', $name, \GuzzleHttp\Psr7\stream_for($resource));
        }
        $request = YfyRequestBuilder::factory()
            ->withEndpoint($uploadLink)
            ->withMethod('POST')
            ->addFile($uploadFile)
            ->build();
        $this->execute($request);
    }

    public function download($fileId, $savePath = null) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_DOWNLOAD_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        $responseBody = json_decode($response->getBody(), true);
        $downloadLink = $responseBody['download_url'];

        $request = YfyRequestBuilder::factory()
            ->withEndpoint($downloadLink)
            ->withMethod('GET')
            ->returnStream(true)
            ->build();
        $response =  $this->execute($request);
        if (!$savePath) {
            return $response->createDownloadFile();
        }
        $response->saveToFile($savePath);
    }

}