<?php

namespace Is\Service;


class WhoIs
{
    private $dir;

    private $fileRegex;

    public function __construct($dir, $fileRegex)
    {
        $this->dir = $dir;
        $this->fileRegex = $fileRegex;
    }

    public function getLogs()
    {
        return array();
    }
}