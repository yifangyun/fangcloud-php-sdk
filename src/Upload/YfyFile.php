<?php
/**
 * 上传文件的封装
 */
namespace Fangcloud\Upload;


use Psr\Http\Message\StreamInterface;

/**
 * 上传文件的封装类
 *
 * Class YfyFile
 * @package Fangcloud\Upload
 */
class YfyFile
{
    /**
     * @var string multipart上传时的name
     */
    private $name;

    /**
     * @var string multipart上传时的filename
     */
    private $filename;

    /**
     * @var string|resource|StreamInterface 上传的文件,可以是文件路径, 可以是resource,也可以是Stream
     */
    private $contents;

    /**
     * YfyFile constructor.
     * @param string $name multipart上传时的name
     * @param string $filename multipart上传时的filename
     * @param StreamInterface|resource|string $contents 上传的文件,可以是文件路径, 可以是resource,也可以是Stream
     */
    public function __construct($name, $filename, $contents)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->contents = $contents;
    }

    /**
     * 获取multipart上传时的name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置multipart上传时的name
     *
     * @param string $name multipart上传时的name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 获取上传文件
     *
     * @return StreamInterface|resource|string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * 设置上传文件
     *
     * @param StreamInterface|resource|string $contents 可以是string,resource或者Stream
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * 获取multipart上传时的filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * 设置multipart上传时的filename
     *
     * @param string $filename multipart上传时的filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }


}