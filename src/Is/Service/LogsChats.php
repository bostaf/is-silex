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
 * Class LogsChats
 * @package Is\Service
 */
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

    public function getChat($id)
    {
        $chat = array();

        $fileName = 'chat-'.$id.'.txt';
        $chatFile = file($this->getDir().$fileName);
        $title = array_shift($chatFile);
        $chat['title'] = $title;

        $chat['contents'] = implode('', $chatFile);

        return $chat;
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