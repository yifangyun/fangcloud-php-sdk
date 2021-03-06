<?php
/**
 * 自定义枚举类
 */

namespace Fangcloud\Constant;

use ReflectionClass;

/**
 * Class Enum
 * @package Fangcloud\Constant
 */
abstract class Enum {
    /**
     * @var array 定义的常量的缓存
     */
    private static $constCacheArray = NULL;

    /**
     * 获取类中定义的所有缓存
     *
     * @return array
     */
    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    /**
     * 校验某个值是否是类中定义的常量
     *
     * @param mixed $value 待校验的值
     * @return bool
     */
    public static function isValid($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values);
    }

    /**
     * 校验是否是定义的type的值
     *
     * @param string $value 待校验value
     * @throws \InvalidArgumentException
     */
    public static function validate($value) {
        if (!static::isValid($value)) {
            $calledClass = get_called_class();
            throw new \InvalidArgumentException("Value should one of constants defined in $calledClass !");
        }
    }
}