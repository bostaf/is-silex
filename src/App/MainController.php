<?php
namespace App;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class MainController implements ControllerProviderInterface {

    public function connect(Application $app) {
        $factory = $app['controllers_factory'];
        /*
        $factory->get('/','App\MainController::home');
        $factory->get('foo','MyApp\MyClassController::doFoo');
        */
        $factory->get('/', function () use ($app) {
            return $app['twig']->render('powitanie.twig', array());
        })->bind('powitanie');
        $factory->get('/historia', function () use ($app) {
            return $app['twig']->render('historia.twig', array());
        })->bind('historia');
        $factory->get('/opis-klanu', function () use ($app) {
            return $app['twig']->render('opis-klanu.twig', array());
        })->bind('opis-klanu');
        $factory->get('/kodeks', function () use ($app) {
            return $app['twig']->render('kodeks.twig', array());
        })->bind('kodeks');
        $factory->get('/umiejetnosci', function () use ($app) {
            return $app['twig']->render('umiejetnosci.twig', array());
        })->bind('umiejetnosci');
        $factory->get('/totemy', function () use ($app) {
            return $app['twig']->render('totemy.twig', array());
        })->bind('totemy');
        $factory->get('/logi', function () use ($app) {
            return $app['twig']->render('logi.twig', array());
        })->bind('logi');
        $factory->get('/o-stronie', function () use ($app) {
            return $app['twig']->render('strona.twig', array());
        })->bind('o-stronie');
        $factory->get('/linki', function () use ($app) {
            return $app['twig']->render('linki.twig', array());
        })->bind('linki');
        $factory->get('/hello/{name}', function ($name) use ($app) {
            return $app['twig']->render('index.twig', array(
                'name' => $name,
            ));
        })->bind('hello');
        return $factory;
    }

    public function home() {
        return 'Hello world';
    }

    public function doFoo() {
        return 'Bar';
    }

}