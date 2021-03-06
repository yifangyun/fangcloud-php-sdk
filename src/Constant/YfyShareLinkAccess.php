<?php
/**
 * 分享访问限制
 */

namespace Fangcloud\Constant;

/**
 * Class YfyShareLinkAccess
 * @package Fangcloud\Constant
 */
final class YfyShareLinkAccess extends Enum
{
    /**
     * 公开访问
     */
    const PUBLIC_ACCESS = 'public';

    /**
     * 仅限公司内部成员访问
     */
    const COMPANY_ACCESS = 'company';
}