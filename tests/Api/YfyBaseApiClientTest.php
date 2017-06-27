<?php

namespace Fangcloud\Test\Api;

use Fangcloud\Api\File\YfyFileClient;
use Fangcloud\Exception\YfyInvalidGrantException;
use Fangcloud\Http\YfyRawResponse;
use Fangcloud\YfyAppInfo;
use Fangcloud\YfyContext;
use Fangcloud\YfyRequest;
use Mockery\MockInterface;

class YfyBaseApiClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var MockInterface */
    private $mockHttpClient;
    /** @var MockInterface */
    private $mockOAuthClient;
    /** @var YfyContext */
    private $yfyContext;
    /** @var YfyFileClient */
    private $fileClient;

    private $fakeNormalResponse = /** @lang JSON */
        "{
  \"success\": true
}";
    private $fakeTokenResponse = /** @lang JSON */
        "{
  \"access_token\": \"fake-access-token\",
  \"token_type\": \"bearer\",
  \"refresh_token\": \"fake-refresh-token\",
  \"scope\": \"all\",
  \"expire_in\": 100
}";
    private $fakeNormalErrorResponse = /** @lang JSON */
        "{
  \"errors\": [
    {
      \"code\": \"fake_code\",
      \"msg\": \"fake message\"
    }
  ],
  \"request_id\": \"fake-request-id\"
}";
    private $fakeInvalidTokenResponse = /** @lang JSON */
        "{
  \"errors\": [
    {
      \"code\": \"invalid_token\",
      \"msg\": \"fake message\"
    }
  ],
  \"request_id\": \"fake-request-id\"
}";
    private $fakeUnauthorizedResponse = /** @lang JSON */
        "{
  \"errors\": [
    {
      \"code\": \"unauthorized\",
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
        $this->mockOAuthClient = \Mockery::mock('Fangcloud\Authentication\OAuthClient');
        $this->yfyContext = new YfyContext();
        $this->yfyContext->setAccessToken('fake-access-token');
        $this->yfyContext->setRefreshToken('fake-refresh-token');
        $this->yfyContext->setAutoRefresh(true);
        $this->fileClient = new YfyFileClient($this->yfyContext, $this->mockHttpClient, $this->mockOAuthClient);
    }

    /**
     * 测试正常请求
     */
    public function testNormalRequest() {
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeNormalResponse), 200));
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试unauthorized
     */
    public function testUnauthorized() {
        $this->expectException('Fangcloud\Exception\YfyAuthorizationRequiredException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeUnauthorizedResponse), 401));
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试无效token,并且不尝试刷新token
     */
    public function testInvalidTokenWithoutRefreshToken() {
        $this->yfyContext->setAutoRefresh(false);
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 401));
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试无效token并且尝试刷新token成功
     */
    public function testInvalidTokenWithRefreshTokenSuccess() {
        $this->yfyContext->setAutoRefresh(true);
        $this->mockHttpClient->shouldReceive('send')
            ->twice()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 401),
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeNormalResponse), 200)
            );
        $this->mockOAuthClient->shouldReceive('refreshToken')
            ->once()->with($this->yfyContext->getRefreshToken())->andReturn(json_decode($this->fakeTokenResponse, true));
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试无效token并且尝试刷新token失败
     */
    public function testInvalidTokenWithRefreshTokenFail() {
        $this->expectException('Fangcloud\Exception\YfyInvalidGrantException');
        $this->yfyContext->setAutoRefresh(true);
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 401)
            );
        $this->mockOAuthClient->shouldReceive('refreshToken')
            ->once()->with($this->yfyContext->getRefreshToken())->andThrow(new YfyInvalidGrantException());
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试请求速率限制
     */
    public function test429() {
        $this->expectException('Fangcloud\Exception\YfyRateLimitException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 429)
            );
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试其他错误
     */
    public function testOtherError() {
        $this->expectException('Fangcloud\Exception\YfySdkException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 403)
            );
        $res = $this->fileClient->getInfo(123);
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeTextResponse), 403)
            );
        $res = $this->fileClient->getInfo(123);
    }

    /**
     * 测试服务器错误
     */
    public function test5xx() {
        $this->expectException('Fangcloud\Exception\YfyServerException');
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->with(\Mockery::on(function ($arg) {
                if (!($arg instanceof YfyRequest)) {
                    return false;
                }
                $headers = $arg->getHeaders();
                if (!array_key_exists('Authorization', $headers) || $headers['Authorization'] !== 'Bearer '.$this->yfyContext->getAccessToken()) {
                    return false;
                }

                return true;
            }))
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 500)
            );
        $res = $this->fileClient->getInfo(123);
        $this->mockHttpClient->shouldReceive('send')
            ->once()
            ->andReturn(
                new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($this->fakeInvalidTokenResponse), 503)
            );
        $res = $this->fileClient->getInfo(123);
    }

}
