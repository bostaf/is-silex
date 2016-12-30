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

    public function getLogs()
    {
        $logs = array();
        $dir_handle = opendir($this->getDir());

        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $logDate = new \DateTime($fileNameArray[1]);
                $logs[$fileNameArray[1]]['date'] = $logDate->format('c');
                $f = fopen($this->getDir().$file, 'r');
                $title = fgets($f);
                fclose($f);
                $logs[$fileNameArray[1]]['logs'][str_replace(['chat-', '.txt'], '', $file)] = $title;
            }
        }

        rsort($logs);
        return $logs;
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