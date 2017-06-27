<?php

namespace Fangcloud\Test;


use Fangcloud\YfyContext;

class YfyContextTest extends \PHPUnit_Framework_TestCase
{
    public function testAutoRefresh() {
        $context = new YfyContext();
        $this->assertEquals(false, $context->isAutoRefresh());

        $context->setAutoRefresh(true);
        $this->assertEquals(false, $context->isAutoRefresh());

        $context->setRefreshToken('xxx');
        $this->assertEquals(true, $context->isAutoRefresh());

        $context->setAutoRefresh(false);
        $this->assertEquals(false, $context->isAutoRefresh());
    }
}
