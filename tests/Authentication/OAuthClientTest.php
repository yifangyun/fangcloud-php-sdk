<?php

namespace Fangcloud\Test\Authentication;


use Fangcloud\Authentication\OAuthClient;
use Fangcloud\Http\YfyRawResponse;
use Fangcloud\YfyRequest;
use GuzzleHttp\Psr7\Stream;
use Mockery\MockInterface;

class OAuthClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var  MockInterface */
    private $mockHttpClient;
    /** @var  OAuthClient */
    private $oauthClient;

    protected function setUp()
    {
        $this->mockHttpClient = \Mockery::mock('Fangcloud\HttpClient\YfyHttpClient');
        $this->oauthClient = new OAuthClient($this->mockHttpClient);
    }

    public function testRefreshToken() {
        $fakeResponseBody = /** @lang JSON */
            "{
  \"access_token\": \"foo\",
  \"token_type\": \"bearer\",
  \"refresh_token\": \"bar\",
  \"scope\": \"all\",
  \"expire_in\": 100
}";
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
                if (!array_key_exists('grant_type', $params) || !$params['grant_type'] === 'refresh') {
                    return false;
                }
                if (!array_key_exists('refresh_token', $params)) {
                    return false;
                }

                return true;
            }))
            ->andReturn(new YfyRawResponse(['X-foo' => 'X-bar'], \GuzzleHttp\Psr7\stream_for($fakeResponseBody), 200));
        $res = $this->oauthClient->refreshToken('foo');
    }


}
