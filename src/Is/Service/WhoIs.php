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
 * Class WhoIs
 * @package Is\Service
 */
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
        $logs = array();
        $dir_handle = opendir($this->getDir());

        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $logDate = new \DateTime($fileNameArray[1] . ' ' . $fileNameArray[2].':'.$fileNameArray[3]);
                $logs[$fileNameArray[1]]['date'] = $logDate->format('c');
                $logs[$fileNameArray[1]]['contents'] = file_get_contents($this->getDir().$file);
            }
        }

        sort($logs);
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