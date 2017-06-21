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
 * Class Members
 * @package Is\Service
 */
class Members
{
    private $dir;

    private $fileRegex;

    private $lineRegexp;

    /**
     * @param string $dir
     * @param string $fileRegex
     * @param string $lineRegex
     */
    public function __construct($dir, $fileRegex, $lineRegex)
    {
        $this->dir = $dir;
        $this->fileRegex = $fileRegex;
        $this->lineRegexp = $lineRegex;
    }

    /**
     * Compares two listings of the 'members' command - the first against the second.
     *
     * @param string $firstList Filename of the first listing. If blank - assumes the most recent listing available in the dir.
     * @param string $secondList Filename of the second listing. If empty - assumes the most previous to the first.
     * @return array Members with some clan stats
     * @todo Add error handling - dirs, files, ...
     */
    public function getMembers($firstList = '', $secondList = '')
    {
        $members = array();
        $members['outcasts'] = array();
        $members['members'] = array();
        $members['date'] = '';
        $firstFileName = '';
        $secondFileName = '';
        $first = array();
        $second = array();

        $dir_handle = opendir($this->getDir());

        if ($firstList == '') { // Grab listings from the dir. Figure out the most recent (first) and the previous (second).

            $files = array();
            while ($file = readdir($dir_handle)) {
                if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                    $membersDate = new \DateTime($fileNameArray[1] . '-' . $fileNameArray[2] . '-' . $fileNameArray[3]);
                    $files[$file]['date'] = $membersDate;
                }
            }
            closedir($dir_handle);

            if (count($files) == 0) return [];

            krsort($files);

            $first = array_slice($files, 0, 1);
            $second = array_slice($files, 1, 1);

            $firstFileName = key($first);
            $firstDate = $first[$firstFileName]['date'];
            $first = $first[$firstFileName];

            $secondFileName = key($second);
            $second = $second[$secondFileName];

        } else if ($secondList != '') { // File names given - just proceed directly to the files

            if (preg_match($this->getFileRegex(), $firstList, $fileNameArray)) {
                $firstFileName = $firstList;
                $secondFileName = $secondList;
                $firstDate = new \DateTime($fileNameArray[1] . '-' . $fileNameArray[2] . '-' . $fileNameArray[3]);
            } else {
                return [];
            }

        } else {
            // todo For the moment both files are required. Here's to determine second, if not given
            return [];
        }

        $lines = file($this->getDir() . $firstFileName);
        foreach ($lines as $line)
            if (preg_match($this->getLineRegexp(), trim($line), $lineArray)) {
                $nick = trim($lineArray[4]);
                $first['members'][$nick]['clevel'] = (int) $lineArray[1];
                $first['members'][$nick]['level'] = (int) $lineArray[2];
                $first['members'][$nick]['profession'] = (string) $lineArray[3];
                $first['members'][$nick]['rank'] = trim($lineArray[5]);
                $first['members'][$nick]['clan_presence'] = '';
                $first['members'][$nick]['clevel_status'] = '';
                $first['members'][$nick]['level_status'] = '';
            }

        $lines = file($this->getDir() . $secondFileName);
        foreach ($lines as $line)
            if (preg_match($this->getLineRegexp(), trim($line), $lineArray)) {
                $nick = trim($lineArray[4]);
                $second['members'][$nick]['clevel'] = (int) $lineArray[1];
                $second['members'][$nick]['level'] = (int) $lineArray[2];
                $second['members'][$nick]['profession'] = (string) $lineArray[3];
                $second['members'][$nick]['rank'] = trim($lineArray[5]);
            }

        foreach ($first['members'] as $nick => $memberData) {
            if (array_key_exists($nick, $second['members'])) {

                $first['members'][$nick]['clan_presence'] = 0;

                if ($first['members'][$nick]['clevel'] < $second['members'][$nick]['clevel'])
                    $first['members'][$nick]['clevel_status'] = 1;
                elseif ($first['members'][$nick]['clevel'] > $second['members'][$nick]['clevel'])
                    $first['members'][$nick]['clevel_status'] = -1;
                else
                    $first['members'][$nick]['clevel_status'] = 0;

                if ($first['members'][$nick]['level'] > $second['members'][$nick]['level'])
                    $first['members'][$nick]['level_status'] = 1;
                elseif ($first['members'][$nick]['level'] < $second['members'][$nick]['level'])
                    $first['members'][$nick]['level_status'] = -1;
                else
                    $first['members'][$nick]['level_status'] = 0;

            } else {
                $first['members'][$nick]['clan_presence'] = 1;
            }
        }

        foreach ($second['members'] as $nick => $memberData) {
            if (!array_key_exists($nick, $first['members'])) {
                $members['outcasts'][] = $nick;
            }
        }

        foreach ($first['members'] as $nick => $memberData) {
            $members['members'][$memberData['clevel']][$nick]['level'] = $memberData['level'];
            $members['members'][$memberData['clevel']][$nick]['rank'] = $memberData['rank'];
            $members['members'][$memberData['clevel']][$nick]['profession'] = $memberData['profession'];
            $members['members'][$memberData['clevel']][$nick]['clevel_status'] = $memberData['clevel_status'];
            $members['members'][$memberData['clevel']][$nick]['level_status'] = $memberData['level_status'];
            $members['members'][$memberData['clevel']][$nick]['clan_presence'] = $memberData['clan_presence'];
        }
        $members['date'] = $firstDate->format('c');

        return $members;
    }

    /**
     * @return array
     */
    public function getMembersWithBios()
    {
        $dir_handle = opendir($this->getDir());

        $files = array();
        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $files[] = $fileNameArray[1];
            }
        }
        closedir($dir_handle);

        sort($files);

        return $files;
    }

    /**
     * @param string $misiak
     * @return array
     */
    public function getMemberData($misiak)
    {
        $membersData = array();
        $dir_handle = opendir($this->getDir());

        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                if ($fileNameArray[1] == $misiak) {
                    $membersData['name'] = $misiak;
                    $membersData['history'] = file_get_contents($this->getDir() . $file);
                }
            }
        }
        closedir($dir_handle);

        return $membersData;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        $files = array();
        $dir_handle = opendir($this->getDir());
        $i = 1;
        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $dateFromFileName = new \DateTime($fileNameArray[1] . '-' . $fileNameArray[2] . '-' . $fileNameArray[3]);
                $files[$i]['file_name_date_Ymd'] = $dateFromFileName->format('Y-m-d');
                $files[$i]['file_date'] = $dateFromFileName->format('c');
                $i++;
            }
        }
        closedir($dir_handle);

        usort($files, function($a, $b) {
           return $a['file_name_date_Ymd'] > $b['file_name_date_Ymd'];
        });
        krsort($files);

        return $files;
    }

    /**
     * Parses input according to the regex from config
     * and creates new entry in data store (new file in data dir)
     * @param string $log Output from in-game members command
     * @return bool
     */
    public function addLog($log)
    {
        if (trim($log) === '') return false;

        $log = explode(PHP_EOL, $log);
        $log = array_map('trim', $log);
        foreach($log as $key => $line) {
            if (preg_match($this->getLineRegexp(), $line) !== 1) {
                unset($log[$key]);
            }
        }

        if (empty($log)) return false;

        $fileCreated = file_put_contents($this->getDir() . '/mem-' . date('Y-m-d') . '.txt', implode(PHP_EOL, $log) . PHP_EOL);

        if (false === $fileCreated) return false;

        return true;
    }

    /**
     * @return string Relative path with trailing slash
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

    /**
     * @return string
     */
    public function getLineRegexp()
    {
        return $this->lineRegexp;
    }
}