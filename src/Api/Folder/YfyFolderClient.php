<?php
/**
 * 文件夹操作
 */
namespace Fangcloud\Api\Folder;


use Fangcloud\Api\YfyBaseApiClient;
use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Constant\YfyFolderListChildrenSortType;
use Fangcloud\Constant\YfySortDirection;
use Fangcloud\Exception\YfySdkException;
use Fangcloud\HttpClient\YfyHttpClient;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\Constant\YfyItemType;

/**
 * Class YfyFolderClient
 * @package Fangcloud\Api\Folder
 */
class YfyFolderClient extends YfyBaseApiClient
{
    const FOLDER_INFO_URI = self::API_PREFIX . '/folder/%s/info';
    const FOLDER_TRASH_INFO_URI = self::API_PREFIX . '/folder/%s/trash';
    const FOLDER_CREATE_URI = self::API_PREFIX . '/folder/create';
    const FOLDER_UPDATE_URI = self::API_PREFIX . '/folder/%s/update';
    const FOLDER_DELETE_URI = self::API_PREFIX . '/folder/%s/delete';
    const FOLDER_DELETE_FROM_TRASH_URI = self::API_PREFIX . '/folder/%s/delete_from_trash';
    const FOLDER_RESTORE_FROM_TRASH_URI = self::API_PREFIX . '/folder/%s/restore_from_trash';
    const FOLDER_MOVE_URI = self::API_PREFIX . '/folder/%s/move';
    const FOLDER_CHILDREN_URI = self::API_PREFIX . '/folder/%s/children';
    const FOLDER_DEPARTMENT_FOLDERS_URI = self::API_PREFIX . '/folder/department_folders';
    const FOLDER_PERSONAL_ITEMS_URI = self::API_PREFIX . '/folder/personal_items';
    const FOLDER_COLLAB_FOLDERS_URI = self::API_PREFIX . '/folder/collab_folders';
    const FOLDER_SHARE_LINKS = self::API_PREFIX . '/folder/%s/share_links';
    const FOLDER_COLLABS = self::API_PREFIX . '/folder/%s/collabs';

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
     * 获取文件夹信息
     *
     * @param int $folderId 文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function getInfo($folderId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取在回收站中的文件夹信息
     *
     * @param int $folderId 文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function getTrashInfo($folderId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_TRASH_INFO_URI)
            ->withMethod('GET')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 创建文件夹
     *
     * @param string $name 文件夹名称
     * @param int $parentId 父文件夹id
     * @param int $departmentId 部门id
     * @return mixed
     * @throws YfySdkException
     */
    public function create($name, $parentId, $departmentId = null) {
        $json = [
            'name' => $name,
            'parent_id' => $parentId
        ];

        if (!empty($departmentId)) {
            $json['departmentId'] = $departmentId;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_CREATE_URI)
            ->withMethod('POST')
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 更新文件夹
     *
     * @param int $folderId 文件夹id
     * @param string $name 更新的文件夹名称
     * @return mixed
     * @throws YfySdkException
     */
    public function update($folderId, $name) {
        $json = [
            'name' => $name
        ];
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_UPDATE_URI)
            ->withMethod('POST')
            ->addPathParam($folderId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 删除文件夹至回收站
     *
     * @param int $folderId 文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function deleteToTrash($folderId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_DELETE_URI)
            ->withMethod('POST')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response = $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 从回收站删除文件夹
     *
     * @param int $folderId 文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function deleteFromTrash($folderId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_DELETE_FROM_TRASH_URI)
            ->withMethod('POST')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 从回收站恢复文件夹
     *
     * @param int $folderId 文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function restoreFromTrash($folderId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_RESTORE_FROM_TRASH_URI)
            ->withMethod('POST')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 移动文件夹
     *
     * @param int $folderId 文件夹id
     * @param int $targetFolderId 目标文件夹id
     * @param int $departmentId 部门id
     * @return mixed
     * @throws YfySdkException
     */
    public function move($folderId, $targetFolderId, $departmentId = null) {
        $json = [
            'target_folder_id' => $targetFolderId
        ];

        if (!empty($departmentId)) {
            $json['departmentId'] = $departmentId;
        }

        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_MOVE_URI)
            ->withMethod('POST')
            ->addPathParam($folderId)
            ->withJson($json)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件夹的单层子文件和文件夹列表
     *
     * @param int $folderId 文件夹id
     * @param int $pageId 页码
     * @param int $pageCapacity 页容量
     * @param string $type 搜索文件类型, 只能是Fangcloud\Constant\YfyItemType中定义的常量
     * @param string $sortBy 搜索类型，只能是Fangcloud\Constant\YfyFolderListChildrenSortType中定义的常量
     * @param string $sortDirection 搜索结果排序顺序，只能是Fangcloud\Constant\YfySortDirection中定义的常量
     * @return mixed
     * @throws YfySdkException
     * @throws \InvalidArgumentException
     *
     * @see YfyItemType
     * @see YfyFolderListChildrenSortType
     * @see YfySortDirection
     */
    public function listChildren($folderId, $pageId = 0, $pageCapacity = 20, $type = YfyItemType::ITEM, $sortBy = null, $sortDirection = null) {
        YfyItemType::validate($type);
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_CHILDREN_URI)
            ->withMethod('GET')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext);
        if (is_int($pageId)) {
            $builder->addQueryParam('page_id', $pageId);
        }
        if (is_int($pageCapacity)) {
            $builder->addQueryParam('page_capacity', $pageCapacity);
        }
        $builder->addQueryParam('type', $type);
        if (!empty($sortBy)) {
            YfyFolderListChildrenSortType::validate($sortBy);
            $builder->addQueryParam('sort_by', $sortBy);
        }
        if (!empty($sortDirection)) {
            YfySortDirection::validate($sortDirection);
            $builder->addQueryParam('sort_direction', $sortDirection);
        }

        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取部门首层文件夹列表
     *
     * @param int $departmentId 部门id
     * @param int $pageId 页码
     * @param int $pageCapacity 页容量
     * @param string $sortBy 搜索类型，只能是Fangcloud\Constant\YfyFolderListChildrenSortType中定义的常量
     * @param string $sortDirection 搜索结果排序顺序，只能是Fangcloud\Constant\YfySortDirection中定义的常量
     * @return mixed
     *
     * @see YfyItemType
     * @see YfyFolderListChildrenSortType
     * @see YfySortDirection
     */
    public function listDepartmentFolders($departmentId, $pageId = 0, $pageCapacity = 20, $sortBy = null, $sortDirection = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_DEPARTMENT_FOLDERS_URI)
            ->withMethod('GET')
            ->addQueryParam('department_id', $departmentId)
            ->withYfyContext($this->yfyContext);

        if (is_int($pageId)) {
            $builder->addQueryParam('page_id', $pageId);
        }
        if (is_int($pageCapacity)) {
            $builder->addQueryParam('page_capacity', $pageCapacity);
        }
        if (!empty($sortBy)) {
            YfyFolderListChildrenSortType::validate($sortBy);
            $builder->addQueryParam('sort_by', $sortBy);
        }
        if (!empty($sortDirection)) {
            YfySortDirection::validate($sortDirection);
            $builder->addQueryParam('sort_direction', $sortDirection);
        }

        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取个人首层文件夹与文件列表
     *
     * @param int $pageId 页码
     * @param int $pageCapacity 页容量
     * @param string $type 搜索文件类型, 只能是Fangcloud\Constant\YfyItemType中定义的常量
     * @param string $sortBy 搜索类型，只能是Fangcloud\Constant\YfyFolderListChildrenSortType中定义的常量
     * @param string $sortDirection 搜索结果排序顺序，只能是Fangcloud\Constant\YfySortDirection中定义的常量
     * @return mixed
     *
     * @see YfyItemType
     * @see YfyFolderListChildrenSortType
     * @see YfySortDirection
     */
    public function listPersonalItems($pageId = 0, $pageCapacity = 20, $type = YfyItemType::ITEM, $sortBy = null, $sortDirection = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_PERSONAL_ITEMS_URI)
            ->withMethod('GET')
            ->withYfyContext($this->yfyContext);

        if (is_int($pageId)) {
            $builder->addQueryParam('page_id', $pageId);
        }
        if (is_int($pageCapacity)) {
            $builder->addQueryParam('page_capacity', $pageCapacity);
        }
        $builder->addQueryParam('type', $type);
        if (!empty($sortBy)) {
            YfyFolderListChildrenSortType::validate($sortBy);
            $builder->addQueryParam('sort_by', $sortBy);
        }
        if (!empty($sortDirection)) {
            YfySortDirection::validate($sortDirection);
            $builder->addQueryParam('sort_direction', $sortDirection);
        }

        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取与我协作的文件夹列表
     *
     * @param int $pageId 页码
     * @param int $pageCapacity 页容量
     * @return mixed
     */
    public function listCollabFolders($pageId = 0, $pageCapacity = 20) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_COLLAB_FOLDERS_URI)
            ->withMethod('GET')
            ->withYfyContext($this->yfyContext);

        if (is_int($pageId)) {
            $builder->addQueryParam('page_id', $pageId);
        }
        if (is_int($pageCapacity)) {
            $builder->addQueryParam('page_capacity', $pageCapacity);
        }

        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件夹的分享链接列表
     *
     * @param int $folderId 文件夹id
     * @param int $pageId 页码
     * @param int $ownerId 分享链接创建者id
     * @return mixed
     * @throws YfySdkException
     */
    public function listShareLinks($folderId, $pageId = 0, $ownerId = null) {
        $builder = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_SHARE_LINKS)
            ->withMethod('GET')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext);
        if (!is_int($pageId)) {
            $builder->addQueryParam('page_id', $pageId);
        }
        if (!is_int($ownerId)) {
            $builder->addQueryParam('owner_id', $ownerId);
        }

        $request = $builder->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取文件夹的协作信息
     *
     * @param int $folderId 文件夹id
     * @return mixed
     * @throws YfySdkException
     */
    public function listCollabs($folderId) {
        $request = YfyRequestBuilder::factory()
            ->withEndpoint(YfyAppInfo::$apiHost . self::FOLDER_COLLABS)
            ->withMethod('GET')
            ->addPathParam($folderId)
            ->withYfyContext($this->yfyContext)
            ->build();
        $response =  $this->execute($request);
        return json_decode($response->getBody(), true);
    }


}