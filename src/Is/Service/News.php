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
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $newsDate = new \DateTime($fileNameArray[1].'-'.$fileNameArray[2].'-'.$fileNameArray[3]);
                $files[$file]['date'] = $newsDate->format('c');
                $files[$file]['author'] = $fileNameArray[4];
                $files[$file]['full_path'] = $this->getDir() . '/'. $file;
                $fp = fopen($files[$file]['full_path'], 'r');
                $files[$file]['contents'] = fread($fp, filesize($files[$file]['full_path']));
                fclose ($fp);
            }
        }
        closedir($dir_handle);

        krsort ($files);
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