<?php

namespace Is\Service;

/**
 * Class News
 * @package Is\Service
 */
class News
{
    private $news;

    private $dir;

    private $fileRegex;

    /**
     * @param string $dir New directory
     * @param string $fileRegex Regex for file name mask
     */
    public function __construct($dir, $fileRegex) {
        $this->dir = $dir;
        $this->fileRegex = $fileRegex;
    }

    public function getNews() {
        $dir_handle = opendir($this->getDir());

        $files = array();
        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $file_array)) {
                $file_array[0] = $this->getDir() . '/'. $file;
                $fp = fopen($file_array[0], 'r');
                $file_array[5] = fread ($fp, filesize($file_array[0]));
                fclose ($fp);
                $files[$file]=$file_array;
            }
        }
        closedir($dir_handle);

        ksort ($files);
        return $files;
    }

    /**
     * @return string
     */
    public function getDir() {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getFileRegex() {
        return $this->fileRegex;
    }
}