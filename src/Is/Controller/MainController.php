<?php

namespace Is\Controller;

use Is\Service\LogsChats;
use Is\Service\Members;
use Is\Service\News;
use Is\Service\WhoIs;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class MainController implements ControllerProviderInterface {

    public function connect(Application $app) {
        $factory = $app['controllers_factory'];

        $factory->get('/', function () use ($app) {
            return $app['twig']->render('powitanie.html.twig', array());
        })->bind('powitanie');
        $factory->get('/historia', function () use ($app) {
            return $app['twig']->render('historia.html.twig', array());
        })->bind('historia');
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
        $factory->get('/aktualnosci', 'Is\Controller\MainController::aktualnosci')->bind('aktualnosci');
        $factory->get('/misiaki', 'Is\Controller\MainController::misiaki')->bind('misiaki');
        $factory->get('/bios', 'Is\Controller\MainController::biosMenu')->bind('bios-menu');
        $factory->get('/misiak/{misiak}', 'Is\Controller\MainController::historiaMisiaka')->assert('misiak', '[A-Za-z]+')->bind('historia-misiaka');
        return $factory;
    }

    public function aktualnosci(Application $app)
    {
        $news = new News($app['config']['data']['news']['dir'], $app['config']['data']['news']['file_regex']);

        return $app['twig']->render('aktualnosci.html.twig', array(
            'news' => $news->getNews()
        ));
    }

    public function misiaki(Application $app)
    {
        $members = new Members(
            $app['config']['data']['members']['dir'],
            $app['config']['data']['members']['file_regex'],
            $app['config']['data']['members']['line_regex']
        );

        return $app['twig']->render('misiaki.html.twig', array(
            'misiaki' => $members->getMembers(),
            'config' => $app['config']['members']
        ));
    }

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
}