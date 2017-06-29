<?php

namespace Fangcloud\Test\Exception;


use Fangcloud\Exception\YfySdkException;
use PHPUnit\Framework\TestCase;

class YfySdkExceptionTest extends TestCase
{
    private $fakeErrors = [
        [
            'code' => 'fake_code',
            'msg' => 'fake message'
        ]
    ];
    public function testCode() {
        $e = new YfySdkException(null, $this->fakeErrors);
        $this->assertEquals('fake_code', $e->getCode());
    }
}