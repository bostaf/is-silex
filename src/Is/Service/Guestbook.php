<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliński <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Is\Service;

/**
 * Class Guestbook
 * @package Is\Service
 */
class Guestbook
{
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

    public function getInscriptions($page = 1) {
        $dir_handle = opendir($this->getDir());

        $files = array();
        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $newsDate = new \DateTime($fileNameArray[1].' '.$fileNameArray[2].':'.$fileNameArray[3].':'.$fileNameArray[4]);
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

        if ($page == 'archiwum') {
            return array_slice($files, 6, count($files));
        } else {
            return array_slice($files, 0, 6);
        }
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