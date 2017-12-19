<?php
/**
 * 搜索过滤类型
 */

namespace Fangcloud\Constant;

/**
 * 搜索过滤类型
 *
 * Class YfyItemSearchQueryFilter
 * @package Fangcloud\Constant
 */
class YfyItemSearchQueryFilter extends Enum
{
    /**
     * 文件名
     */
    const FILE_NAME = 'file_name';

    /**
     * 文件内容
     */
    const CONTENT = 'content';

    /**
     * 创建者
     */
    const CREATOR = 'creator';

    /**
     * 全部
     */
    const ALL = 'all';
}