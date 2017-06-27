<?php

namespace Fangcloud\Test\Http;

use Fangcloud\Http\YfyRawResponse;
use PHPUnit\Framework\TestCase;

class YfyRawResponseTest extends TestCase
{
    private $fakeRawHeader = <<<HEADER
HTTP/1.1 200 OK
Content-Type: application/json; charset=UTF-8
X-foo: bar\r\n\r\n
HEADER;

    private $fakeHeaderArray = [
        'Content-Type' => 'application/json; charset=UTF-8',
        'X-foo' => 'bar'
    ];

    private $fakeJson = /** @lang JSON */
        "{
  \"foo\": \"bar\"
}";
    private $fakeText = /** @lang HTML */
        "<html>
<head><title>503 Service Temporarily Unavailable</title></head>
<body bgcolor=\"white\">
<center><h1>503 Service Temporarily Unavailable</h1></center>
<hr><center>nginx/1.9.12</center>
</body>
</html>";

    public function testSetHeaderFromString() {
        $response = new YfyRawResponse($this->fakeRawHeader, \GuzzleHttp\Psr7\stream_for($this->fakeJson));
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeHeaderArray, $response->getHeaders());
    }

    public function testSetHeaderFromArray() {
        $response = new YfyRawResponse($this->fakeHeaderArray, \GuzzleHttp\Psr7\stream_for($this->fakeJson), 200);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeHeaderArray, $response->getHeaders());
    }

    public function testJsonBody() {
        $response = new YfyRawResponse($this->fakeRawHeader, \GuzzleHttp\Psr7\stream_for($this->fakeJson));
        $this->assertEquals(json_decode($this->fakeJson, true), json_decode($response->getBody(), true));
    }

    public function testTextBody() {
        $response = new YfyRawResponse($this->fakeRawHeader, \GuzzleHttp\Psr7\stream_for($this->fakeText));
        $this->assertEquals($this->fakeText, $response->getBody()->getContents());
    }


}