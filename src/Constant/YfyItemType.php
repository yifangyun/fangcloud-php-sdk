<?php
/**
 * Item类型
 */

namespace Fangcloud\Constant;

/**
 * Class YfyItemType
 * @package Fangcloud
 */
final class YfyItemType extends Enum
{
    /**
     * 文件类型
     */
    const FILE = 'file';

    /**
     * 文件夹类型
     */
    const FOLDER = 'folder';

    /**
     * item类型, 表示包含file和folder两种类型
     */
    const ITEM = 'item';
}