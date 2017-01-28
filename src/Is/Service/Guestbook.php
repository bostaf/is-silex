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
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Guestbook
 * @package Is\Service
 */
class Guestbook
{
    private $dir;

    private $fileRegex;

    private  $postsPerPage;

    /**
     * @param string $dir New directory
     * @param string $fileRegex Regex for file name mask
     */
    public function __construct($dir, $fileRegex, $postsPerPage) {
        $this->dir = $dir;
        $this->fileRegex = $fileRegex;
        $this->postsPerPage = $postsPerPage;
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

        return array_slice($inscriptions, ($page - 1) * $this->getPostsPerPage(), $this->getPostsPerPage());
    }

    /**
     * @return int
     */
    public function getNumberOfPages()
    {
        $dir_handle = opendir($this->getDir());
        $i = 0;
        while ($file = readdir($dir_handle)) if (preg_match($this->getFileRegex(), $file)) $i++;
        return (int) ceil($i / $this->getPostsPerPage());
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function addPost(Request $request)
    {
        $date = new \DateTime('now');
        $nick = trim($request->request->get('guestbook-nick'));
        $wpis = trim($request->request->get('guestbook-wpis'));

        if ($nick == '' or $wpis == '') return false;

        $data[] = 'Nick:' . substr($nick, 0, 25) . PHP_EOL;
        $data[] = 'Dodano:' . $date->format('Y-m-d (H:i:s)') . PHP_EOL;
        $data[] = 'Url:' . substr(trim($request->request->get('guestbook-url')), 0, 25) . PHP_EOL;
        $data[] = 'Email:' . substr(trim($request->request->get('guestbook-email')), 0, 25) . PHP_EOL;
        $data[] = 'Wpis:' . substr($wpis, 0, 1500) . PHP_EOL;
        file_put_contents($this->getDir() . 'guestbook-' . $date->format('Y-m-d-H-i-s') . '.txt', $data);
        //var_dump('<pre>', $request->request->all());die();
        return true;
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

    /**
     * @return int
     */
    public function getPostsPerPage()
    {
        return $this->postsPerPage;
    }
}