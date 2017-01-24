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

        $inscriptions = array();
        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $newsDate = new \DateTime($fileNameArray[1].' '.$fileNameArray[2].':'.$fileNameArray[3].':'.$fileNameArray[4]);
                $inscriptions[$file]['date'] = $newsDate->format('c');
                $inscriptions[$file]['author'] = $fileNameArray[4];
                $inscriptions[$file]['full_path'] = $this->getDir() . $file;
                $contentsLines = file($inscriptions[$file]['full_path']);
                $inscriptions[$file]['nick'] = '';
                $inscriptions[$file]['email'] = '';
                $inscriptions[$file]['url'] = '';
                $inscriptions[$file]['contents'] = [];
                foreach ($contentsLines as $line) {
                    if (substr($line, 0, 5) == 'Nick:') {
                        $inscriptions[$file]['nick'] = substr($line, 5, strlen($line) - 6);
                    } elseif (substr($line, 0, 7) == 'Dodano:') {
                        continue;
                    } elseif (substr($line, 0, 4) == 'Url:') {
                        $inscriptions[$file]['url'] = substr($line, 4, strlen($line) - 5);
                    } elseif (substr($line, 0, 6) == 'Email:') {
                        $inscriptions[$file]['email'] = substr($line, 6, strlen($line) - 7);
                    } elseif (substr($line, 0, 5) == 'Wpis:') {
                        $wpis[] = substr($line, 5, strlen($line) - 5);
                    } else {
                        $wpis[] = $line;
                    }
                }
                $inscriptions[$file]['contents'] = join('', $wpis);
                unset($wpis);
            }
        }
        closedir($dir_handle);

        krsort ($inscriptions);

        if ($page == 'archiwum') {
            return array_slice($inscriptions, 6, count($inscriptions));
        } else {
            return array_slice($inscriptions, 0, 6);
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