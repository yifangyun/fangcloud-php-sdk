<?php
/**
 * 被邀请协作对象类型
 */

namespace Fangcloud\Constant;

/**
 * 被邀请协作对象类型
 *
 * Class YfyCollabSubType
 * @package Fangcloud\Constant
 */
class YfyCollabSubType extends Enum
{
    /**
     * 用户
     */
    const USER = 'user';

    /**
     * 群组
     */
    const GROUP = 'group';

    /**
     * 部门
     */
    const DEPARTMENT = 'department';
}