<?php

namespace Fangcloud\Upload;


use Psr\Http\Message\StreamInterface;

class YfyFile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string|resource|StreamInterface
     */
    private $contents;

    /**
     * YfyFile constructor.
     * @param string $name
     * @param string $filename
     * @param StreamInterface|resource|string $contents
     */
    public function __construct($name, $filename, $contents)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return StreamInterface|resource|string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param StreamInterface|resource|string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }


}