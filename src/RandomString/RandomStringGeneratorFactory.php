<?php

namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;
use InvalidArgumentException;

class RandomStringGeneratorFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * Pseudo random string generator creation.
     *
     * @param RandomStringGenerator|string|null $generator
     *
     * @throws InvalidArgumentException If the pseudo random string generator must be set to "random_bytes", "mcrypt", "openssl", or "urandom", or be an instance of Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface.
     *
     * @return RandomStringGenerator
     */
    public static function createPseudoRandomStringGenerator($generator = null)
    {
        if (!$generator) {
            return self::detectDefaultPseudoRandomStringGenerator();
        }

        if ($generator instanceof RandomStringGenerator) {
            return $generator;
        }

        if ('random_bytes' === $generator) {
            return new RandomBytesRandomStringGenerator();
        }
        if ('mcrypt' === $generator) {
            return new McryptRandomStringGenerator();
        }
        if ('openssl' === $generator) {
            return new OpenSslRandomStringGenerator();
        }
        if ('urandom' === $generator) {
            return new UrandomRandomStringGenerator();
        }

        throw new InvalidArgumentException('The pseudo random string generator must be set to "random_bytes", "mcrypt", "openssl", or "urandom", or be an instance of Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface');
    }

    /**
     * Detects which pseudo-random string generator to use.
     *
     * @throws YfySdkException If unable to detect a cryptographically secure pseudo-random string generator.
     *
     * @return RandomStringGenerator
     */
    private static function detectDefaultPseudoRandomStringGenerator()
    {
        // Check for PHP 7's CSPRNG first to keep mcrypt deprecation messages from appearing in PHP 7.1.
        if (function_exists('random_bytes')) {
            return new RandomBytesRandomStringGenerator();
        }

        // Since openssl_random_pseudo_bytes() can sometimes return non-cryptographically
        // secure pseudo-random strings (in rare cases), we check for mcrypt_create_iv() next.
        if (function_exists('mcrypt_create_iv')) {
            return new McryptRandomStringGenerator();
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return new OpenSslRandomStringGenerator();
        }

        if (!ini_get('open_basedir') && is_readable('/dev/urandom')) {
            return new UrandomRandomStringGenerator();
        }

        throw new YfySdkException('Unable to detect a cryptographically secure pseudo-random string generator.');
    }
}
