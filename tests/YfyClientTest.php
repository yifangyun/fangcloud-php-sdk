<?php

use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use PHPUnit\Framework\TestCase;

class YfyClientTest extends TestCase
{
    public function testYfyClient() {
        $client = new \Fangcloud\YfyClient();
        $client->users();
    }
}