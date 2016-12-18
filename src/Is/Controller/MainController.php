<?php

namespace Is\Controller;

use Is\Service\News;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class MainController implements ControllerProviderInterface {

    public function connect(Application $app) {
        $factory = $app['controllers_factory'];
        /*
        $factory->get('/','Is\MainController::home');
        $factory->get('foo','MyApp\MyClassController::doFoo');
        */
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
        $factory->get('/o-stronie', function () use ($app) {
            return $app['twig']->render('strona.html.twig', array());
        })->bind('o-stronie');
        $factory->get('/linki', function () use ($app) {
            return $app['twig']->render('linki.html.twig', array());
        })->bind('linki');
        $factory->get('/aktualnosci','Is\Controller\MainController::aktualnosci')->bind('aktualnosci');
        $factory->get('/hello/{name}', function ($name) use ($app) {
            return $app['twig']->render('index.html.twig', array(
                'name' => $name,
            ));
        })->bind('hello');
        return $factory;
    }

    public function aktualnosci(Application $app) {
        $news = new News();
        $news->getNews();

        return $app['twig']->render('aktualnosci.html.twig', array(
            'news' => $news
        ));
    }

    public function doFoo() {
        return 'Bar';
    }

}