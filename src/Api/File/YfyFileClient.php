<?php
/**
 * 文件操作
 */
namespace Fangcloud\Api\File;

use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\Upload\YfyFile;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequestBuilder;
use Psr\Http\Message\StreamInterface;

/**
 * Class YfyFileClient
 * @package Fangcloud\Api\File
 */
class YfyFileClient extends YfyBaseApiClient
{
    const FILE_INFO_URI = self::API_PREFIX . '/file/%s/info';
    const FILE_TRASH_INFO_URI = self::API_PREFIX . '/file/%s/trash';
    const FILE_UPDATE_URI = self::API_PREFIX . '/file/%s/update';
    const FILE_DELETE_URI = self::API_PREFIX . '/file/%s/delete';
    const FILE_DELETE_FROM_TRASH_URI = self::API_PREFIX . '/file/%s/delete_from_trash';
    const FILE_RESTORE_FROM_TRASH_URI = self::API_PREFIX . '/file/%s/restore_from_trash';
    const FILE_MOVE_URI = self::API_PREFIX . '/file/%s/move';
    const FILE_COPY_URI = self::API_PREFIX . '/file/%s/copy';
    const FILE_UPLOAD_URI = self::API_PREFIX . '/file/upload';
    const FILE_UPLOAD_NEW_VERSION_URI = self::API_PREFIX . '/file/%s/new_version';
    const FILE_DOWNLOAD_URI = self::API_PREFIX . '/file/%s/download';
    const FILE_SHARE_LINKS_URI = self::API_PREFIX . '/file/%s/share_links';
    const FILE_COMMENTS_URI = self::API_PREFIX . '/file/%s/comments';
    const FILE_VERSIONS_URI = self::API_PREFIX . '/file/%s/versions';
    const FILE_VERSION_INFO_URI = self::API_PREFIX . '/file/%s/version/%s/info';
    const FILE_VERSION_PROMOTE_URI = self::API_PREFIX . '/file/%s/version/%s/promote';
    const FILE_VERSION_DELETE_URI = self::API_PREFIX . '/file/%s/version/%s/delete';

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
     * 获取文件信息
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取在回收站中的文件信息
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function getTrashInfo($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_TRASH_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 更新文件信息
     *
     * @param int $fileId 文件id
     * @param string $name 更新的文件名
     * @param string|null $description 更新的描述
     * @return mixed
     */
    public function update($fileId, $name, $description = null) {
        $json = [
            'name' => $name,
            'description' => $description
        ];

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除文件至回收站
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function deleteToTrash($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_DELETE_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 从回收站删除文件
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function deleteFromTrash($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_DELETE_FROM_TRASH_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 从回收站恢复文件
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function restoreFromTrash($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_RESTORE_FROM_TRASH_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 移动文件
     *
     * @param int $fileId 文件id
     * @param int $targetFolderId 目标文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function move($fileId, $targetFolderId) {
        $json = [
            'target_folder_id' => $targetFolderId
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_MOVE_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 复制文件
     *
     * @param int $fileId 文件id
     * @param int $targetFolderId 目标文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function copy($fileId, $targetFolderId) {
        $json = [
            'target_folder_id' => $targetFolderId
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_COPY_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件的分享链接列表
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function listShareLinks($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_SHARE_LINKS_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件的评论列表
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function listComments($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_COMMENTS_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件的所有版本列表
     *
     * @param int $fileId 文件id
     * @return mixed
     * @throws YfySdkException
     */
    public function listVersions($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_VERSIONS_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件的特定版本信息
     *
     * @param int $fileId 文件id
     * @param int $versionId 文件版本id
     * @return mixed
     * @throws YfySdkException
     */
    public function getVersionInfo($fileId, $versionId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_VERSION_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->addPathParam($versionId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 设置某版本为当前版本
     *
     * @param int $fileId 文件id
     * @param int $versionId 文件版本id
     * @return mixed
     * @throws YfySdkException
     */
    public function promoteVersion($fileId, $versionId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_VERSION_PROMOTE_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->addPathParam($versionId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除文件版本
     *
     * @param int $fileId 文件id
     * @param int $versionId 文件版本id
     * @return mixed
     * @throws YfySdkException
     */
    public function deleteVersion($fileId, $versionId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_VERSION_DELETE_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->addPathParam($versionId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取上传文件url
     *
     * @param int $parentId 父文件夹id
     * @param string $name 上传文件名
     * @return string 上传链接
     * @throws YfySdkException
     */
    public function getUploadFileUrl($parentId, $name) {
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
        return $responseBody['presign_url'];
    }

    /**
     * 获取上传文件新版本url
     *
     * @param int $fileId 文件id
     * @param string $name 新版本文件名
     * @param string $remark 备注
     * @return string 上传链接
     * @throws YfySdkException
     */
    public function getUploadNewVersionUrl($fileId, $name, $remark) {
        // presign
        $json = [
            'name' => $name,
            'remark' => $remark,
            'upload_type' => 'api'
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_UPLOAD_NEW_VERSION_URI)
            ->withMethod('POST')
            ->addPathParam($fileId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody['presign_url'];
    }

    /**
     * 上传文件
     *
     * @param int $parentId 父文件夹id
     * @param string $name 上传文件名
     * @param resource|StreamInterface|string $resource
     * @return mixed
     * @throws YfySdkException
     */
    public function uploadFile($parentId, $name, $resource) {
        $uploadLink = $this->getUploadFileUrl($parentId, $name);

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
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 上传文件新版本
     *
     * @param int $fileId 文件id
     * @param string $name 上传文件名
     * @param string $remark 备注
     * @param resource|StreamInterface|string $resource
     * @return mixed
     * @throws YfySdkException
     */
    public function uploadNewVersion($fileId, $name, $remark, $resource) {
        $uploadLink = $this->getUploadNewVersionUrl($fileId, $name, $remark);

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
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取下载文件的url
     *
     * @param int $fileId 下载文件id
     * @return string 下载链接
     * @throws YfySdkException
     */
    public function getDownloadUrl($fileId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FILE_DOWNLOAD_URI)
            ->withMethod('GET')
            ->addPathParam($fileId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody['download_url'];
    }

    /**
     * 下载文件
     *
     * @param string $fileId 文件路径
     * @param string|null $savePath 保存的目录或者文件名
     * @return \Fangcloud\Download\DownloadFile 若savePath为null,返回DownloadFile,其中包含下载的文件流
     * @throws YfySdkException
     */
    public function download($fileId, $savePath = null) {
        $downloadLink = $this->getDownloadUrl($fileId);

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