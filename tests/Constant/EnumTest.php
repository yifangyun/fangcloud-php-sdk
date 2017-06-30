<?php

use Fangcloud\Constant\YfyItemType;

class EnumTest extends \PHPUnit\Framework\TestCase
{
    public function testValidateSuccess() {
        YfyItemType::validate(YfyItemType::FILE);
    }

    public function testValidateFail() {
        $this->expectException('\InvalidArgumentException');
        YfyItemType::validate('some');
    }
}