<?php

namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;

class McryptRandomStringGenerator implements RandomStringGenerator
{
    use RandomStringGeneratorTrait;

    /**
     * @const string The error message when generating the string fails.
     */
    const ERROR_MESSAGE = 'Unable to generate a cryptographically secure pseudo-random string from mcrypt_create_iv(). ';

    /**
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
     * @inheritdoc
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
