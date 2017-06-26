<?php
/**
 * 随机字符串的random bytes实现
 */
namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;

/**
 * Class RandomBytesRandomStringGenerator
 * @package Fangcloud\RandomString
 */
class RandomBytesRandomStringGenerator implements RandomStringGenerator
{
    use RandomStringGeneratorTrait;

    /**
     * @const string The error message when generating the string fails.
     */
    const ERROR_MESSAGE = 'Unable to generate a cryptographically secure pseudo-random string from random_bytes(). ';

    /**
     * RandomBytesRandomStringGenerator constructor.
     * @throws YfySdkException
     */
    public function __construct()
    {
        if (!function_exists('random_bytes')) {
            throw new YfySdkException(
                static::ERROR_MESSAGE .
                'The function random_bytes() does not exist.'
            );
        }
    }

    /**
     * {@inheritdoc}
     * @param string $length
     * @return string
     */
    public function getRandomString($length)
    {
        $this->validateLength($length);

        return $this->binToHex(random_bytes($length), $length);
    }
}
