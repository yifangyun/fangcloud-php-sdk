<?php
/**
 * 随机字符串生成器工厂
 */
namespace Fangcloud\RandomString;

use Fangcloud\Exception\YfySdkException;
use InvalidArgumentException;

/**
 * Class RandomStringGeneratorFactory
 * @package Fangcloud\RandomString
 */
class RandomStringGeneratorFactory
{
    /**
     * RandomStringGeneratorFactory constructor.
     */
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * 创建一个随机字符串生成器
     *
     * @param string|RandomStringGenerator|null $generator 可以一个RandomStringGenerator实例, 也可以是'random_bytes','mcrypt','openssl','urandom'四种其中一种的string, 若不提供则会自动检测默认实现
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
     * 检测默认的RandomStringGenerator实现
     *
     * @throws YfySdkException
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
