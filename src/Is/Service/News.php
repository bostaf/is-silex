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
    /**
     * @param string $dir New directory
     */
    public function __construct($dir) {
        $this->dir = $dir;
    }

    public function getNews() {
        $dir_handle = opendir($this->getDir());

        $files = array();
        while ($file = readdir($dir_handle)) {
            if (preg_match('/news-([0-9]{4})-([0-9]{2})-([0-9]{2})-([A-Z,a-z]*).txt/', $file, $file_array)) {
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
}