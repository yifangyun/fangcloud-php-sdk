<?php

namespace Fangcloud\Test;


use Fangcloud\YfyAppInfo;

class YfyAppInfoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @after testCheckInitFail
     */
    public function testCheckInit()
    {
        YfyAppInfo::init('xxx', 'xxx', 'xxx');
        YfyAppInfo::checkInit();
    }

    public function testCheckInitFail()
    {
        $this->expectException('Fangcloud\Exception\YfySdkException');
        YfyAppInfo::checkInit();
    }
    
}
