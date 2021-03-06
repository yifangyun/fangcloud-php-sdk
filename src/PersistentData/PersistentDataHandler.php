<?php
/**
 * 持久化数据的处理
 * 参考Facebook
 */
namespace Fangcloud\PersistentData;

/**
 * Interface PersistentDataInterface
 *
 * @package Facebook
 */
interface PersistentDataHandler
{
    /**
     * 从持久化数据存储中根据key获取一个value
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * 向持久化数据存储中存储一对key,value
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value);
}
