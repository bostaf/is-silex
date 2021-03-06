<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliński <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Is\Controller;

use Is\Service\Guestbook;
use Is\Service\LogsChats;
use Is\Service\Members;
use Is\Service\News;
use Is\Service\WhoIs;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MainController
 * @package Is\Controller
 */
class MainController implements ControllerProviderInterface {
    /**
     * @param Application $app
     * @return mixed controllers_factory
     */
    public function connect(Application $app) {
        /**
         * @var $factory \Silex\ControllerCollection
         */
        $factory = $app['controllers_factory'];
        $factory->get('/', function () use ($app) {
            return $app['twig']->render('powitanie.html.twig', array());
        })->bind('powitanie');
        $factory->get('/historia', 'Is\Controller\MainController::historia')->bind('historia');
        $factory->get('/opis-klanu', function () use ($app) {
            return $app['twig']->render('opis-klanu.html.twig', array());
        })->bind('opis-klanu');
        $factory->get('/kodeks', function () use ($app) {
            return $app['twig']->render('kodeks.html.twig', array());
        })->bind('kodeks');
        $factory->get('/umiejetnosci', function () use ($app) {
            return $app['twig']->render('umiejetnosci.html.twig', array());
        })->bind('umiejetnosci');
        $factory->get('/totemy', function () use ($app) {
            return $app['twig']->render('totemy.html.twig', array());
        })->bind('totemy');
        $factory->get('/logi', function () use ($app) {
            return $app['twig']->render('logi.html.twig', array());
        })->bind('logi');
        $factory->get('/logi/rozmowy', 'Is\Controller\MainController::rozmowy')->bind('rozmowy');
        $factory->get('/logi/rozmowa/{id}', 'Is\Controller\MainController::rozmowa')->assert('id', '\d{4}-\d{2}-\d{2}-\d+')->bind('rozmowa');
        $factory->get('/logi/members', function () use ($app) {
            return $app['twig']->render('logi-members.html.twig', array());
        })->bind('logi-members');
        $factory->get('/logi/who-is', 'Is\Controller\MainController::whoIs')->bind('who-is');
        $factory->get('/o-stronie', function () use ($app) {
            return $app['twig']->render('strona.html.twig', array());
        })->bind('o-stronie');
        $factory->get('/linki', function () use ($app) {
            return $app['twig']->render('linki.html.twig', array());
        })->bind('linki');
        $factory->get('/aktualnosci/{page}', 'Is\Controller\MainController::aktualnosci')->value('page', 'main')->bind('aktualnosci');
        $factory->get('/misiaki/{firstLog}/{secondLog}', 'Is\Controller\MainController::misiaki')->value('firstLog', '')->value('secondLog', '')->bind('misiaki');
        $factory->get('/bios', 'Is\Controller\MainController::biosMenu')->bind('bios-menu');
        $factory->get('/misiak/{misiak}', 'Is\Controller\MainController::historiaMisiaka')->assert('misiak', '[A-Za-z]+')->bind('historia-misiaka');
        $factory->get('/cygnus-division', function () use ($app) {
            return $app['twig']->render('cygnus.html.twig', array());
        })->bind('cygnus-division');
        $factory->get('/ksiega-gosci/{page}', 'Is\Controller\MainController::guestbook')->value('page', 1)->bind('ksiega-gosci');
        $factory->post('/ksiega-gosci/{page}', 'Is\Controller\MainController::guestbookAddPost')->value('page', 1);


        $factory->get('/login', function(Request $request) use ($app) {
            return $app['twig']->render('user/login.html.twig', array(
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));
        })->bind('login');
        $factory->get('/user/password', 'Is\Controller\MainController::userPassword')->bind('user-password');
        $factory->post('/user/password', 'Is\Controller\MainController::userPasswordSubmit');


        if ($app['config']['app']['require_https']) {
            $factory->requireHttps();
        }

        return $factory;
    }
    /**
     * @param Application $app
     * @param string $page
     * @return mixed
     */
    public function aktualnosci(Application $app, $page)
    {
        $news = new News($app['config']['data']['news']['dir'], $app['config']['data']['news']['file_regex']);
        return $app['twig']->render('aktualnosci.html.twig', array(
            'news' => $news->getNews($page),
            'page' => $page
        ));
    }
    public function historia(Application $app)
    {
        // check if there are any bios
        $bios = new Members(
            $app['config']['data']['bios']['dir'],
            $app['config']['data']['bios']['file_regex'],
            ''
        );
        return $app['twig']->render('historia.html.twig', array(
            'count_bios' => count($bios->getMembersWithBios())
        ));
    }
    /**
     * @param Application $app
     * @param Request $request
     * @param string $firstLog
     * @param string $secondLog
     * @return mixed
     */
    public function misiaki(Application $app, Request $request, $firstLog, $secondLog)
    {
        // check if there are any bios
        $bios = new Members(
            $app['config']['data']['bios']['dir'],
            $app['config']['data']['bios']['file_regex'],
            ''
        );
        $logsFromUrl['first'] = $request->attributes->get('firstLog');
        $logsFromUrl['second'] = $request->attributes->get('secondLog');
        $members = new Members(
            $app['config']['data']['members']['dir'],
            $app['config']['data']['members']['file_regex'],
            $app['config']['data']['members']['line_regex']
        );
        if ($firstLog != '' and $secondLog != '') {
            $firstLog = 'mem-' . $firstLog . '.txt';
            $secondLog = 'mem-' . $secondLog . '.txt';
        }
        return $app['twig']->render('misiaki.html.twig', array(
            'misiaki' => $members->getMembers($firstLog, $secondLog),
            'config' => $app['config']['members'],
            'files_list' => $members->getFiles(),
            'logs_from_url' =>$logsFromUrl,
            'count_bios' => count($bios->getMembersWithBios())
        ));
    }
    /**
     * @param Application $app
     * @return mixed
     */
    public function biosMenu(Application $app)
    {
        $bios = new Members(
            $app['config']['data']['bios']['dir'],
            $app['config']['data']['bios']['file_regex'],
            ''
        );
        return $app['twig']->render('bios_menu.html.twig', array(
            'bios' => $bios->getMembersWithBios(),
            'config' => $app['config']['members']
        ));
    }
    /**
     * @param Application $app
     * @param string $misiak
     * @return mixed
     */
    public function historiaMisiaka(Application $app, $misiak)
    {
        $bios = new Members(
            $app['config']['data']['bios']['dir'],
            $app['config']['data']['bios']['file_regex'],
            ''
        );
        return $app['twig']->render('misiak.html.twig', array(
            'misiak' => $bios->getMemberData($misiak)
        ));
    }
    /**
     * @param Application $app
     * @return mixed
     */

    public function rozmowy(Application $app)
    {
        $chats = new LogsChats(
            $app['config']['data']['logs-chats']['dir'],
            $app['config']['data']['logs-chats']['file_regex']
        );
        return $app['twig']->render('rozmowy.html.twig', array(
            'chats' => $chats->getLogs()
        ));
    }
    /**
     * @param Application $app
     * @param string $id
     * @return mixed
     */

    public function rozmowa(Application $app, $id)
    {
        $chats = new LogsChats(
            $app['config']['data']['logs-chats']['dir'],
            $app['config']['data']['logs-chats']['file_regex']
        );
        return $app['twig']->render('rozmowa.html.twig', array(
            'rozmowa' => $chats->getChat($id)
        ));
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function whoIs(Application $app)
    {
        $logs = new WhoIs(
            $app['config']['data']['logs-who-is']['dir'],
            $app['config']['data']['logs-who-is']['file_regex']
        );
        return $app['twig']->render('logi-who-is.html.twig', array(
            'logs' => $logs->getLogs()
        ));
    }

    public function guestbook(Application $app, $page)
    {
        $guestBook = new Guestbook(
            $app['config']['data']['guestbook']['dir'],
            $app['config']['data']['guestbook']['file_regex'],
            $app['config']['guestbook']['posts_per_page']
        );
        return $app['twig']->render('ksiega-gosci.html.twig', array(
            'inscriptions' => $guestBook->getInscriptionsForPage($page),
            'page' => $page,
            'pages' => $guestBook->getNumberOfPages()
        ));
    }

    public function guestbookAddPost(Application $app, Request $request, $page)
    {
        $guestBook = new Guestbook(
            $app['config']['data']['guestbook']['dir'],
            $app['config']['data']['guestbook']['file_regex'],
            $app['config']['guestbook']['posts_per_page']
        );
        $allInscriptions = $guestBook->getInscriptions();
        $inscriptions = $guestBook->getInscriptionsForPage();
        $lastPostDateOffset = null;
        if (count($allInscriptions) > 0)
            $lastPostDateOffset = $guestBook->getDatetimeOfLatestPost()->modify($app['config']['guestbook']['max_frequency']);
        if ($lastPostDateOffset > new \DateTime('now')) {
            return $app['twig']->render('ksiega-gosci.html.twig', array(
                'inscriptions' => $inscriptions,
                'page' => $page,
                'pages' => $guestBook->getNumberOfPages(),
                'time_wait' => $lastPostDateOffset->diff(new \DateTime('now'))->format('%i minut')
            ));
        }
        if ( ! $guestBook->addPost($request)) {
            return $app['twig']->render('ksiega-gosci.html.twig', array(
                'inscriptions' => $inscriptions,
                'page' => $page,
                'pages' => $guestBook->getNumberOfPages(),
                'errors' => true
            ));
        }
        return $app->redirect($app->path('ksiega-gosci'));
    }

    public function userPassword(Application $app)
    {
        return $app['twig']->render('user/password.html.twig', array(
            'error' => false
        ));
    }

    public function userPasswordSubmit(Application $app, Request $request)
    {
        /**
         * @var $user \Is\Security\User\User;
         */
        $user = $app['security.token_storage']->getToken()->getUser();
        $encoder = $app['security.encoder_factory']->getEncoder($user);

        $postCurrentPass = trim($request->request->get('current_password'));
        $postNewPass = trim($request->request->get('new_password'));
        $postRepeatNewPass = trim($request->request->get('repeat_new_password'));

        $currentPasswordIsValid = $encoder->isPasswordValid($user->getPassword(), $postCurrentPass, $user->getSalt());
        $newPasswordIsValid = $postNewPass == $postRepeatNewPass;

        if (!($currentPasswordIsValid && $newPasswordIsValid)) {
            return $app['twig']->render('user/password.html.twig', array(
                'error' => true,
            ));
        }
        // persist new password
        $user->setPassword($encoder->encodePassword($postNewPass, $user->getSalt()));
        // todo flush user
        /**
         * @var $um \Is\Security\User\UserManager
         */
        $um = $app['user_manager'];
        $um->flushUser($user);
        // redirect somewhere
        return $app->redirect($app->path('powitanie'));
    }
}
