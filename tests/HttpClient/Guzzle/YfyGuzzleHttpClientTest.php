<?php

namespace Fangcloud\Test\HttpClient\Guzzle;

use Fangcloud\Http\YfyRequestBuilder;
use Fangcloud\Upload\YfyFile;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Fangcloud\HttpClient\Guzzle\YfyGuzzleHttpClient;

class YfyGuzzleHttpClientTest extends TestCase
{
    /** @var MockInterface */
    private $mockClient;
    /** @var  YfyGuzzleHttpClient */
    private $guzzleHttpClient;

    private $fakeJsonResponse = /** @lang JSON */
        "{
  \"foo\": \"bar\"
}";
    private $fakeResponseHeaders = [
        'X-foo' => 'bar'
    ];

    protected function setUp()
    {
        $this->mockClient = \Mockery::mock('GuzzleHttp\Client');
        $this->guzzleHttpClient = new YfyGuzzleHttpClient($this->mockClient);
    }

    /**
     * 测试正常Get
     */
    public function testGet() {
        $request = YfyRequestBuilder::factory()
            ->withMethod('GET')
            ->withEndpoint('http://foo.bar/%s')
            ->addPathParam(123)
            ->addQueryParam('key', 'value')
            ->build();

        $options = [
            'headers' => $request->getHeaders(),
            'timeout' => $request->getTimeout(),
            'connect_timeout' => $request->getConnectTimeout(),
            'verify' => false,
            'stream' => $request->isStream(),
            'query' => [
                'key' => 'value'
            ]
        ];

        $this->mockClient->shouldReceive('request')
            ->once()
            ->withArgs([$request->getMethod(), $request->getUrl(), $options])
            ->andReturn(new Response(200, $this->fakeResponseHeaders, $this->fakeJsonResponse));
        $response = $this->guzzleHttpClient->send($request);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeResponseHeaders, $response->getHeaders());
        $this->assertEquals($this->fakeJsonResponse, $response->getBody()->getContents());
    }

    /**
     * 测试json
     */
    public function testJsonRequest() {
        $request = YfyRequestBuilder::factory()
            ->withMethod('POST')
            ->withEndpoint('http://foo.bar/%s')
            ->addPathParam(123)
            ->withJson(['foo' => 'bar'])
            ->build();

        $options = [
            'headers' => $request->getHeaders(),
            'timeout' => $request->getTimeout(),
            'connect_timeout' => $request->getConnectTimeout(),
            'verify' => false,
            'stream' => $request->isStream(),
            'json' => [
                'foo' => 'bar'
            ]
        ];

        $this->mockClient->shouldReceive('request')
            ->once()
            ->withArgs([$request->getMethod(), $request->getUrl(), $options])
            ->andReturn(new Response(200, $this->fakeResponseHeaders, $this->fakeJsonResponse));
        $this->guzzleHttpClient->send($request);
        $response = $this->guzzleHttpClient->send($request);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeResponseHeaders, $response->getHeaders());
        $this->assertEquals($this->fakeJsonResponse, $response->getBody()->getContents());
    }

    /**
     * 测试form
     */
    public function testFormRequest() {
        $request = YfyRequestBuilder::factory()
            ->withMethod('POST')
            ->withEndpoint('http://foo.bar/%s')
            ->addPathParam(123)
            ->withFormParams(['foo' => 'bar'])
            ->build();

        $options = [
            'headers' => $request->getHeaders(),
            'timeout' => $request->getTimeout(),
            'connect_timeout' => $request->getConnectTimeout(),
            'verify' => false,
            'stream' => $request->isStream(),
            'form_params' => [
                'foo' => 'bar'
            ]
        ];

        $this->mockClient->shouldReceive('request')
            ->once()
            ->withArgs([$request->getMethod(), $request->getUrl(), $options])
            ->andReturn(new Response(200, $this->fakeResponseHeaders, $this->fakeJsonResponse));
        $this->guzzleHttpClient->send($request);
        $response = $this->guzzleHttpClient->send($request);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeResponseHeaders, $response->getHeaders());
        $this->assertEquals($this->fakeJsonResponse, $response->getBody()->getContents());
    }

    /**
     * 测试multipart
     */
    public function testMultipartRequest() {
        $request = YfyRequestBuilder::factory()
            ->withMethod('POST')
            ->withEndpoint('http://foo.bar/%s')
            ->addPathParam(123)
            ->addFile(new YfyFile('xxx', 'xxx', 'xxx'))
            ->build();

        $options = [
            'headers' => $request->getHeaders(),
            'timeout' => $request->getTimeout(),
            'connect_timeout' => $request->getConnectTimeout(),
            'verify' => false,
            'stream' => $request->isStream(),
            'multipart' => [[
                    'name' => 'xxx',
                    'filename' => 'xxx',
                    'contents' => 'xxx'
                ]]
        ];

        $this->mockClient->shouldReceive('request')
            ->once()
            ->withArgs([$request->getMethod(), $request->getUrl(), $options])
            ->andReturn(new Response(200, $this->fakeResponseHeaders, $this->fakeJsonResponse));
        $this->guzzleHttpClient->send($request);
        $response = $this->guzzleHttpClient->send($request);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeResponseHeaders, $response->getHeaders());
        $this->assertEquals($this->fakeJsonResponse, $response->getBody()->getContents());
    }

    /**
     * 测试multipart
     */
    public function testException() {
        $request = YfyRequestBuilder::factory()
            ->withMethod('GET')
            ->withEndpoint('http://foo.bar/%s')
            ->addPathParam(123)
            ->build();

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andThrow(new BadResponseException(
                null,
                new Request($request->getMethod(), $request->getUrl()),
                new Response(400, $this->fakeResponseHeaders, $this->fakeJsonResponse)
                )
            );
        $this->guzzleHttpClient->send($request);
        $response = $this->guzzleHttpClient->send($request);
        $this->assertEquals(400, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeResponseHeaders, $response->getHeaders());
        $this->assertEquals($this->fakeJsonResponse, $response->getBody()->getContents());
    }

    /**
     * 测试stream
     * 会发送真的请求!!!
     */
    public function testStream() {
        $client = new YfyGuzzleHttpClient();
        $request = YfyRequestBuilder::factory()
            ->withMethod('GET')
            ->withEndpoint('https://www.fangcloud.com/')
            ->returnStream(true)
            ->build();
        $res = $client->send($request);
        $this->assertEquals(false, $res->getBody()->isSeekable());

        $request = YfyRequestBuilder::factory()
            ->withMethod('GET')
            ->withEndpoint('https://www.fangcloud.com/')
            ->build();
        $res = $client->send($request);
        $this->assertEquals(true, $res->getBody()->isSeekable());
    }

}