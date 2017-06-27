<?php

namespace Fangcloud\Test\Authentication;


use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Http\YfyRawResponse;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyRequest;
use Mockery\MockInterface;

class OAuthClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var  MockInterface */
    private $mockHttpClient;
    /** @var  MockInterface */
    private $mockPersistentDataHandler;
    /** @var  MockInterface */
    private $mockRandomStringGenerator;
    /** @var  OAuthClient */
    private $oauthClient;

    private $fakeTokenResponse = /** @lang JSON */
        "{
  \"access_token\": \"foo\",
  \"token_type\": \"bearer\",
  \"refresh_token\": \"bar\",
  \"scope\": \"all\",
  \"expire_in\": 100
}";

    private $fakeErrorResponse = /** @lang JSON */
        "{
  \"errors\": [
    {
      \"code\": \"fake_code\",
      \"msg\": \"fake message\"
    }
  ],
  \"request_id\": \"fake-request-id\"
}";

    private $fakeTextResponse = /** @lang HTML */
        "<html>
<head><title>503 Service Temporarily Unavailable</title></head>
<body bgcolor=\"white\">
<center><h1>503 Service Temporarily Unavailable</h1></center>
<hr><center>nginx/1.9.12</center>
</body>
</html>";

    protected function setUp()
    {
        YfyAppInfo::init('fake-client', 'fake-secret', 'http://foo.bar');
        $this->mockHttpClient = \Mockery::mock('Fangcloud\HttpClient\YfyHttpClient');
        $this->mockPersistentDataHandler = \Mockery::mock('Fangcloud\PersistentData\PersistentDataHandler');
        $this->mockRandomStringGenerator = \Mockery::mock('Fangcloud\RandomString\RandomStringGenerator');
        $this->oauthClient = new OAuthClient($this->mockHttpClient, $this->mockPersistentDataHandler, $this->mockRandomStringGenerator);
    }

    /**
     * 测试刷新token
     */
    public function testRefreshToken() {
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                $params = $arg->getFormParams();
                if (!array_key_exists('Authorization', $headers)) {
                    return false;
                }
                if (!array_key_exists('grant_type', $params) || $params['grant_type'] !== 'refresh') {
                    return false;
                }
                if (!array_key_exists('refresh_token', $params)) {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeTokenResponse), 200));
        $res = $this->oauthClient->refreshToken('foo');
    }

    /**
     * 测试获取授权url
     */
    public function testGetAuthorizationUrl() {
        $this->mockRandomStringGenerator->shouldReceive('getRandomString')
            ->once()->andReturn('fakeState');
        $this->mockPersistentDataHandler->shouldReceive('set')
            ->once();
        $url = $this->oauthClient->getAuthorizationUrl();
        $this->assertSame(YfyAppInfo::$authHost . '/oauth/authorize?response_type=code&client_id=fake-client&redirect_uri=http%3A%2F%2Ffoo.bar&state=fakeState', $url);
    }

    /**
     * 测试回调获取token
     */
    public function testFinishAuthorizationCodeFlow() {
        $code = 'abc';
        $state = 'xxx';
        $this->mockPersistentDataHandler->shouldReceive('get')
            ->once()
            ->andReturn('xxx');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                $params = $arg->getFormParams();
                if (!array_key_exists('Authorization', $headers)) {
                    return false;
                }
                if (!array_key_exists('grant_type', $params) || $params['grant_type'] !== 'authorization_code') {
                    return false;
                }
                if (!array_key_exists('code', $params) || $params['code'] !== 'abc') {
                    return false;
                }
                if (!array_key_exists('redirect_uri', $params) || $params['redirect_uri'] !== YfyAppInfo::$redirectUri) {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeTokenResponse), 200));
        $this->oauthClient->finishAuthorizationCodeFlow($code, $state);
    }

    /**
     * 测试回调时state校验失败
     */
    public function testFinishAuthorizationCodeFlowInvalidState() {
        $code = 'abc';
        $state = 'xxx';
        $this->mockPersistentDataHandler->shouldReceive('get')
            ->once()
            ->andReturn('xxxx');
        $this->expectException('Fangcloud\Exception\YfyInvalidStateException');
        $this->oauthClient->finishAuthorizationCodeFlow($code, $state);
    }

    /**
     * 测试密码模式
     */
    public function testGetTokenByPasswordFlow() {
        $username = 'fakeusername';
        $password = 'fakepassword';
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                $params = $arg->getFormParams();
                if (!array_key_exists('Authorization', $headers)) {
                    return false;
                }
                if (!array_key_exists('grant_type', $params) || $params['grant_type'] !== 'password') {
                    return false;
                }
                if (!array_key_exists('username', $params) || $params['username'] !== 'fakeusername') {
                    return false;
                }
                if (!array_key_exists('password', $params) || $params['password'] !== 'fakepassword') {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeTokenResponse), 200));
        $res = $this->oauthClient->getTokenByPasswordFlow($username, $password);
    }

    /**
     * 测试401返回
     */
    public function test401() {
        $this->expectException('Fangcloud\Exception\YfyUnauthorizedException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeErrorResponse), 401));
        $this->oauthClient->getTokenByPasswordFlow('fakeusername', 'fakepassword');
    }

    /**
     * 测试服务器错误
     */
    public function test5xx() {
        $this->expectException('Fangcloud\Exception\YfyServerException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeErrorResponse), 500));
        $this->oauthClient->getTokenByPasswordFlow('fakeusername', 'fakepassword');

        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeErrorResponse), 503));
        $this->oauthClient->getTokenByPasswordFlow('fakeusername', 'fakepassword');
    }

    /**
     * 测试其他错误
     */
    public function testOtherError() {
        $this->expectException('Fangcloud\Exception\YfySdkException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeErrorResponse), 403));
        $this->oauthClient->getTokenByPasswordFlow('fakeusername', 'fakepassword');

        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeTextResponse), 403));
        $this->oauthClient->getTokenByPasswordFlow('fakeusername', 'fakepassword');
    }

}
