<?php

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Rpodwika\Silex\YamlConfigServiceProvider(__DIR__.'/../src/config/config.yml'));

$app['debug'] = $app['config']['app']['debug'];

// Controller
$app->mount('/', new App\MainController());

$app->run();
