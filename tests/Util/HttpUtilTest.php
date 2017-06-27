<?php

namespace Fangcloud\Test\Util;

use Fangcloud\Util\HttpUtil;

class HttpUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testDetectFilenameSuccess() {
        $headers['Content-Disposition'] = 'attachment; filename=user_profile_pic_12030_f0e9.jpg';
        $filename = HttpUtil::detectFilename($headers);
        $this->assertSame($filename, 'user_profile_pic_12030_f0e9.jpg');
        $headers['Content-Disposition'] = 'attachment; filename="user_profile_pic_12030_f0e9.jpg";filename*="xxxxx"';
        $filename = HttpUtil::detectFilename($headers);
        $this->assertSame($filename, 'user_profile_pic_12030_f0e9.jpg');
    }

    /**
     * @expectedException \Fangcloud\Exception\YfySdkException
     */
    public function testDetectFilenameFail() {
        $headers = [];
        $filename = HttpUtil::detectFilename($headers);
    }
}