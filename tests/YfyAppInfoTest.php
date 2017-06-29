<?php

namespace Fangcloud\Test;


use Fangcloud\YfyAppInfo;

class YfyAppInfoTest extends \PHPUnit_Framework_TestCase
{

    public function testCheckInitFail()
    {
        YfyAppInfo::reset();
        $this->expectException('Fangcloud\Exception\YfySdkException');
        YfyAppInfo::checkInit();
    }

    /**
     * @depends testCheckInitFail
     */
    public function testCheckInit()
    {
        YfyAppInfo::reset();
        YfyAppInfo::init('xxx', 'xxx', 'xxx');
        YfyAppInfo::checkInit();
    }

}
