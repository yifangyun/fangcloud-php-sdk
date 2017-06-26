<?php
/**
 * 下载文件的包装类
 */
namespace Fangcloud\Download;


use Psr\Http\Message\StreamInterface;

/**
 * Class DownloadFile
 * @package Fangcloud\Download
 */
class DownloadFile
{
    /**
     * @var string 下载的文件名
     */
    private $name;

    /**
     * @var StreamInterface 下载的文件流
     */
    private $stream;

    /**
     * DownloadFile constructor.
     * @param string $name
     * @param StreamInterface $stream
     */
    public function __construct($name, StreamInterface $stream)
    {
        $this->name = $name;
        $this->stream = $stream;
    }

    /**
     * 获取下载的文件名
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置下载的文件名
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 获取下载的文件流
     *
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * 设置下载的文件流
     *
     * @param StreamInterface $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

}