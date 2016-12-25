<?php

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

    public function __construct($dir, $fileRegex, $lineRegex)
    {
        $this->dir = $dir;
        $this->fileRegex = $fileRegex;
        $this->lineRegexp = $lineRegex;
    }

    /**
     * @return array Members with some stats
     * @todo All in one currently but should be separated to allow comparison toggle and custom dates. Also currently it assumes there's at least 2 files in the dir...
     */
    public function getMembers()
    {
        $dir_handle = opendir($this->getDir());

        $files = array();
        while ($file = readdir($dir_handle)) {
            if (preg_match($this->getFileRegex(), $file, $fileNameArray)) {
                $membersDate = new \DateTime($fileNameArray[1].'-'.$fileNameArray[2].'-'.$fileNameArray[3]);
                $files[$file]['date'] = $membersDate;
            }
        }
        closedir($dir_handle);

        krsort($files);

        $current = array_slice($files, 0, 1);
        $currentFileName = key($current);
        $currentDate = $current[$currentFileName]['date'];
        $current = $current[$currentFileName];
        $lines = file($this->getDir() . $currentFileName);
        foreach ($lines as $line)
            if (preg_match($this->getLineRegexp(), $line, $lineArray)) {
                $nick = trim($lineArray[4]);
                $current['members'][$nick]['clevel'] = (int) $lineArray[1];
                $current['members'][$nick]['level'] = (int) $lineArray[2];
                $current['members'][$nick]['profession'] = (string) $lineArray[3];
                $current['members'][$nick]['rank'] = trim($lineArray[5]);
                $current['members'][$nick]['clan_presence'] = '';
                $current['members'][$nick]['clevel_status'] = '';
                $current['members'][$nick]['level_status'] = '';
            }

        $old = array_slice($files, 1, 1);
        $oldFileName = key($old);
        $old = $old[$oldFileName];
        $lines = file($this->getDir() . $oldFileName);
        foreach ($lines as $line)
            if (preg_match($this->getLineRegexp(), $line, $lineArray)) {
                $nick = trim($lineArray[4]);
                $old['members'][$nick]['clevel'] = (int) $lineArray[1];
                $old['members'][$nick]['level'] = (int) $lineArray[2];
                $old['members'][$nick]['profession'] = (string) $lineArray[3];
                $old['members'][$nick]['rank'] = trim($lineArray[5]);
            }

        foreach ($current['members'] as $nick => $memberData) {
            if (array_key_exists($nick, $old['members'])) {

                $current['members'][$nick]['clan_presence'] = 0;

                if ($current['members'][$nick]['clevel'] < $old['members'][$nick]['clevel'])
                    $current['members'][$nick]['clevel_status'] = 1;
                elseif ($current['members'][$nick]['clevel'] > $old['members'][$nick]['clevel'])
                    $current['members'][$nick]['clevel_status'] = -1;
                else
                    $current['members'][$nick]['clevel_status'] = 0;

                if ($current['members'][$nick]['level'] > $old['members'][$nick]['level'])
                    $current['members'][$nick]['level_status'] = 1;
                elseif ($current['members'][$nick]['level'] < $old['members'][$nick]['level'])
                    $current['members'][$nick]['level_status'] = -1;
                else
                    $current['members'][$nick]['level_status'] = 0;

            } else {
                $current['members'][$nick]['clan_presence'] = 1;
            }
        }

        $members = array();
        foreach ($current['members'] as $nick => $memberData) {
            $members['members'][$memberData['clevel']][$nick]['level'] = $memberData['level'];
            $members['members'][$memberData['clevel']][$nick]['rank'] = $memberData['rank'];
            $members['members'][$memberData['clevel']][$nick]['profession'] = $memberData['profession'];
            $members['members'][$memberData['clevel']][$nick]['clevel_status'] = $memberData['clevel_status'];
            $members['members'][$memberData['clevel']][$nick]['level_status'] = $memberData['level_status'];
            $members['members'][$memberData['clevel']][$nick]['clan_presence'] = $memberData['clan_presence'];
        }
        $members['date'] = $currentDate->format('c');

        return $members;
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

    /**
     * @return string
     */
    public function getLineRegexp()
    {
        return $this->lineRegexp;
    }
}