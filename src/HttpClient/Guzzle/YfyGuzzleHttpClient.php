<?php
/**
 * 使用guzzle库对HttpClient的实现
 */
namespace Fangcloud\HttpClient\Guzzle;

use Fangcloud\Http\YfyRawResponse;

use Fangcloud\HttpClient\AbstractYfyHttpClient;
use Fangcloud\Upload\YfyFile;
use Fangcloud\YfyRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class YfyGuzzleHttpClient
 * @package Fangcloud\HttpClient\Guzzle
 */
class YfyGuzzleHttpClient extends AbstractYfyHttpClient
{
    /**
     * @var \GuzzleHttp\Client The Guzzle client.
     */
    protected $guzzleClient;

    /**
     * YfyGuzzleHttpClient constructor.
     * @param Client|null $guzzleClient guzzle client
     */
    public function __construct(Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
    }

    /**
     * {@inheritdoc}
     * @param YfyRequest $yfyRequest
     * @return YfyRawResponse
     */
    public function send(YfyRequest $yfyRequest)
    {
        $options = [
            'headers' => $yfyRequest->getHeaders(),
            'timeout' => $yfyRequest->getTimeout(),
            'connect_timeout' => $yfyRequest->getConnectTimeout(),
            'verify' => false, // TODO: 加证书
            'stream' => $yfyRequest->isStream()
        ];

        if ($yfyRequest->hasQuery()) {
            $options['query'] = $yfyRequest->getQueryParams();
        }

        if ($yfyRequest->isJson()) {
            $options['json'] = $yfyRequest->getJson();
        }
        elseif ($yfyRequest->isForm()) {
            $options['form_params'] = $yfyRequest->getFormParams();
        }
        elseif ($yfyRequest->isMultipart()) {
            $multipart_options = [];
            // $multipart_options[] = $yfyRequest->getFormParams();
            /** @var YfyFile $yfyFile */
            foreach ($yfyRequest->getFiles() as $yfyFile) {
                $eachFile = array(
                    'name' => $yfyFile->getName(),
                    'contents' => $yfyFile->getContents(),
                    'filename' => $yfyFile->getFilename()
                );
                $multipart_options[] = $eachFile;
            }
            $options['multipart'] = $multipart_options;
        }

        try {
            $rawResponse = $this->guzzleClient->request($yfyRequest->getMethod(), $yfyRequest->getUrl(), $options);
        } catch (BadResponseException $e) {
            // network io error will throw runtime exception
            $rawResponse = $e->getResponse();
        }

        $rawHeaders = $this->getHeadersAsString($rawResponse);
        $rawBody = $rawResponse->getBody();
        $httpStatusCode = $rawResponse->getStatusCode();

        return new YfyRawResponse($rawHeaders, $rawBody, $httpStatusCode);
    }

    /**
     * Returns the Guzzle array of headers as a string.
     *
     * @param ResponseInterface $response The Guzzle response.
     *
     * @return string
     */
    public function getHeadersAsString(ResponseInterface $response)
    {
        $headers = $response->getHeaders();
        $rawHeaders = [];
        foreach ($headers as $name => $values) {
            $rawHeaders[] = $name . ": " . implode(", ", $values);
        }

        return implode("\r\n", $rawHeaders);
    }
}
