<?php
/**
 * Created by PhpStorm.
 * User: just-cj
 * Date: 2017/6/27
 * Time: 21:25
 */

namespace Fangcloud\Test\Http;


use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyClient;

class YfyRequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicAuth() {
        $username = 'fakeusername';
        $password = 'fakepassword';
        $request = YfyRequestBuilder::factory()
            ->withBasicAuth($username, $password)
            ->build();
        $headers = $request->getHeaders();
        $this->assertEquals('Basic '.base64_encode($username.':'.$password), $headers['Authorization']);
    }

    public function testPathParams() {
        $template = 'http://foo.bar/%s/%s';
        $request = YfyRequestBuilder::factory()
            ->withEndpoint($template)
            ->addPathParam(123)
            ->addPathParam(456)
            ->build();
        $url = $request->getUrl();
        $this->assertEquals('http://foo.bar/123/456', $url);
    }

    public function testUserAgent() {
        YfyAppInfo::init('fake-client-id', 'fake-client-secret', 'http://foo.bar');
        $request = YfyRequestBuilder::factory()->build();
        $headers = $request->getHeaders();
        $this->assertEquals('fake-client-id '.'OfficialFangcloudPhpSDK/'.YfyClient::VERSION, $headers['User-Agent']);
    }

    public function testRuntimeVersion() {
        $request = YfyRequestBuilder::factory()->build();
        $headers = $request->getHeaders();
        $this->assertEquals(phpversion(), $headers['X-Runtime-Version']);
    }
}
