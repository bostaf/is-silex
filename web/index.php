<?php

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));


$app->get('/', function () use ($app) {
    return $app['twig']->render('powitanie.twig', array());
})->bind('powitanie');
$app->get('/historia', function () use ($app) {
    return $app['twig']->render('historia.twig', array());
})->bind('historia');
$app->get('/opis-klanu', function () use ($app) {
    return $app['twig']->render('opis-klanu.twig', array());
})->bind('opis-klanu');
$app->get('/kodeks', function () use ($app) {
    return $app['twig']->render('kodeks.twig', array());
})->bind('kodeks');
$app->get('/umiejetnosci', function () use ($app) {
    return $app['twig']->render('umiejetnosci.twig', array());
})->bind('umiejetnosci');
$app->get('/totemy', function () use ($app) {
    return $app['twig']->render('totemy.twig', array());
})->bind('totemy');
$app->get('/logi', function () use ($app) {
    return $app['twig']->render('logi.twig', array());
})->bind('logi');
$app->get('/o-stronie', function () use ($app) {
    return $app['twig']->render('strona.twig', array());
})->bind('o-stronie');
$app->get('/linki', function () use ($app) {
    return $app['twig']->render('linki.twig', array());
})->bind('linki');
$app->get('/hello/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => $name,
    ));
})
    ->bind('hello');

$app->run();
