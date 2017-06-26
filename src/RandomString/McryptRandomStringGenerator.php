<?php
/**
 * 随机字符串的mcrypt实现
 */
namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;

/**
 * Class McryptRandomStringGenerator
 * @package Fangcloud\RandomString
 */
class McryptRandomStringGenerator implements RandomStringGenerator
{
    use RandomStringGeneratorTrait;

    /**
     * @const string The error message when generating the string fails.
     */
    const ERROR_MESSAGE = 'Unable to generate a cryptographically secure pseudo-random string from mcrypt_create_iv(). ';

    /**
     * McryptRandomStringGenerator constructor.
     * @throws YfySdkException
     */
    public function __construct()
    {
        if (!function_exists('mcrypt_create_iv')) {
            throw new YfySdkException(
                static::ERROR_MESSAGE .
                'The function mcrypt_create_iv() does not exist.'
            );
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

        $binaryString = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);

        if ($binaryString === false) {
            throw new YfySdkException(
                static::ERROR_MESSAGE .
                'mcrypt_create_iv() returned an error.'
            );
        }

        return $this->binToHex($binaryString, $length);
    }
}
