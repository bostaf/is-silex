<?php

namespace Is\Service;


class LogsChats
{
    private $dir;

    private $fileRegex;

    public function __construct($dir, $fileRegex)
    {
        $this->dir = $dir;
        $this->fileRegex = $fileRegex;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getFileRegex()
    {
        return $this->fileRegex;
    }

}