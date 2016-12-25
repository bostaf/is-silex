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
                $files[$file]['date'] = $membersDate->format('c');
            }
        }
        closedir($dir_handle);

        krsort($files);

        $current = array_slice($files, 0, 1);
        $lines = file($this->getDir() . key($current));
        foreach ($lines as $line)
            if (preg_match($this->getLineRegexp(), $line, $lineArray)) {
                $nick = trim($lineArray[4]);
                $current[key($current)]['members'][$nick]['clevel'] = (int) $lineArray[1];
                $current[key($current)]['members'][$nick]['level'] = (int) $lineArray[2];
                $current[key($current)]['members'][$nick]['profession'] = (string) $lineArray[3];
                $current[key($current)]['members'][$nick]['rank'] = trim($lineArray[5]);
                $current[key($current)]['members'][$nick]['clan_presence'] = '';
                $current[key($current)]['members'][$nick]['clevel_status'] = '';
                $current[key($current)]['members'][$nick]['level_status'] = '';
            }

        $old = array_slice($files, 1, 1);
        $lines = file($this->getDir() . key($old));
        foreach ($lines as $line)
            if (preg_match($this->getLineRegexp(), $line, $lineArray)) {
                $nick = trim($lineArray[4]);
                $old[key($old)]['members'][$nick]['clevel'] = (int) $lineArray[1];
                $old[key($old)]['members'][$nick]['level'] = (int) $lineArray[2];
                $old[key($old)]['members'][$nick]['profession'] = (string) $lineArray[3];
                $old[key($old)]['members'][$nick]['rank'] = trim($lineArray[5]);
            }

        foreach ($current[key($current)]['members'] as $nick => $memberData) {
            if (array_key_exists($nick, $old[key($old)]['members'])) {

                $current[key($current)]['members'][$nick]['clan_presence'] = 0;

                if ($current[key($current)]['members'][$nick]['clevel'] < $old[key($old)]['members'][$nick]['clevel'])
                    $current[key($current)]['members'][$nick]['clevel_status'] = 1;
                elseif ($current[key($current)]['members'][$nick]['clevel'] > $old[key($old)]['members'][$nick]['clevel'])
                    $current[key($current)]['members'][$nick]['clevel_status'] = -1;
                else
                    $current[key($current)]['members'][$nick]['clevel_status'] = 0;

                if ($current[key($current)]['members'][$nick]['level'] > $old[key($old)]['members'][$nick]['level'])
                    $current[key($current)]['members'][$nick]['level_status'] = 1;
                elseif ($current[key($current)]['members'][$nick]['level'] < $old[key($old)]['members'][$nick]['level'])
                    $current[key($current)]['members'][$nick]['level_status'] = -1;
                else
                    $current[key($current)]['members'][$nick]['level_status'] = 0;

            } else {
                $current[key($current)]['members'][$nick]['clan_presence'] = 1;
            }
        }


        return $current;
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