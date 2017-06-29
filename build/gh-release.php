<?php
/*
 * Creates a Github API release using the changelog contents. Attaches aws.zip
 * and aws.phar to the release.
 *
 * The OAUTH_TOKEN environment variable is required.
 *
 *     Usage: php gh-release.php X.Y.Z
 */

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7;

$owner = 'yifangyun';
$repo = 'fangcloud-php-sdk';
$token = getenv('OAUTH_TOKEN') or die('An OAUTH_TOKEN environment variable is required');
isset($argv[1]) or die('Usage php gh-release.php X.Y.Z');
$tag = $argv[1];

// Grab and validate the tag annotation
chdir(dirname(__DIR__));
$message = `chag contents -t "$tag"` or die('Chag could not find or parse the tag');

// Create a GitHub client.
$client = new GuzzleHttp\Client([
    'base_uri' => 'https://api.github.com/',
    'headers' => ['Authorization' => "token $token"],
]);

// Create the release
$response = $client->post("repos/${owner}/${repo}/releases", [
    'json' => [
        'tag_name'   => $tag,
        'name'       => "Version {$tag}",
        'body'       => $message,
    ]
]);

// Grab the location of the new release
$url = $response->getHeaderLine('Location');
echo "Release successfully published to: $url\n";

// Uploads go to uploads.github.com
$uploadUrl = new Uri($url);
$uploadUrl = $uploadUrl->withHost('uploads.github.com');

// Upload aws.zip
$response = $client->post($uploadUrl . '/assets?name=fangcloud-php-sdk.zip', [
    'headers' => ['Content-Type' => 'application/zip'],
    'body'    => Psr7\try_fopen(__DIR__ . '/artifacts/fangcloud-php-sdk.zip', 'r')
]);
echo "fangcloud-php-sdk.zip uploaded to: " . json_decode($response->getBody(), true)['browser_download_url'] . "\n";

// Upload aws.phar
$response = $client->post($uploadUrl . '/assets?name=fangcloud-php-sdk.phar', [
    'headers' => ['Content-Type' => 'application/phar'],
    'body'    => Psr7\try_fopen(__DIR__ . '/artifacts/fangcloud-php-sdk.phar', 'r')
]);
echo "fangcloud-php-sdk.phar uploaded to: " . json_decode($response->getBody(), true)['browser_download_url'] . "\n";
