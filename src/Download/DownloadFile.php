<?php

namespace Fangcloud\Download;


use Psr\Http\Message\StreamInterface;

class DownloadFile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var StreamInterface
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
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param StreamInterface $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

}