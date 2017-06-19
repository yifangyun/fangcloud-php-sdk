<?php

namespace Fangcloud\RandomString;


interface RandomStringGenerator
{
    /**
     * 生成一个指定长度的随机字符串
     *
     * @param $length string 随机字符串的长度
     * @return string
     */
    public function getRandomString($length);
}