<?php
/**
 * 协作角色列表
 */

namespace Fangcloud\Constant;

/**
 * 协作角色列表
 *
 * Class YfyCollabRole
 * @package Fangcloud\Constant
 */
final class YfyCollabRole extends Enum
{
    /**
     * 编辑者
     */
    const EDITOR = 'editor';

    /**
     * 查看者
     */
    const VIEWER = 'viewer';

    /**
     * 预览者
     */
    const PREVIEWER = 'previewer';

    /**
     * 上传者
     */
    const UPLOADER = 'uploader';

    /**
     * 预览上传者
     */
    const PREVIEWER_UPLOADER = 'previewer_uploader';

    /**
     * 共同所有者
     */
    const COOWNER = 'coowner';

    /**
     * 查看上传者
     */
    const VIEWER_UPLOADER = 'viewer_uploader';

}