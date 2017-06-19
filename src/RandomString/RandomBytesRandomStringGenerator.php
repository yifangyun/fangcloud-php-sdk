<?php

namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;

class RandomBytesRandomStringGenerator implements RandomStringGenerator
{
    use RandomStringGeneratorTrait;

    /**
     * @const string The error message when generating the string fails.
     */
    const ERROR_MESSAGE = 'Unable to generate a cryptographically secure pseudo-random string from random_bytes(). ';

    /**
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
     * @inheritdoc
     */
    public function getRandomString($length)
    {
        $this->validateLength($length);

        return $this->binToHex(random_bytes($length), $length);
    }
}
