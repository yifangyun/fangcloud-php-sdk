<?php
/**
 * 随机字符串的openssl实现
 */
namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;

/**
 * Class OpenSslRandomStringGenerator
 * @package Fangcloud\RandomString
 */
class OpenSslRandomStringGenerator implements RandomStringGenerator
{
    use RandomStringGeneratorTrait;

    /**
     * @const string The error message when generating the string fails.
     */
    const ERROR_MESSAGE = 'Unable to generate a cryptographically secure pseudo-random string from openssl_random_pseudo_bytes().';

    /**
     * OpenSslRandomStringGenerator constructor.
     * @throws YfySdkException
     */
    public function __construct()
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new YfySdkException(static::ERROR_MESSAGE . 'The function openssl_random_pseudo_bytes() does not exist.');
        }
    }

    /**
     * {@inheritdoc}
     * @param string $length
     * @return string
     * @throws YfySdkException
     */
    public function getRandomString($length)
    {
        $this->validateLength($length);

        $wasCryptographicallyStrong = false;
        $binaryString = openssl_random_pseudo_bytes($length, $wasCryptographicallyStrong);

        if ($binaryString === false) {
            throw new YfySdkException(static::ERROR_MESSAGE . 'openssl_random_pseudo_bytes() returned an unknown error.');
        }

        if ($wasCryptographicallyStrong !== true) {
            throw new YfySdkException(static::ERROR_MESSAGE . 'openssl_random_pseudo_bytes() returned a pseudo-random string but it was not cryptographically secure and cannot be used.');
        }

        return $this->binToHex($binaryString, $length);
    }
}
