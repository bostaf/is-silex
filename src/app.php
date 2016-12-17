<?php

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/views',
));
$app->register(new Rpodwika\Silex\YamlConfigServiceProvider(__DIR__.'/../app/config/config.yml'));

$app['debug'] = $app['config']['app']['debug'];

// Controller
$app->mount('/', new Is\Controller\MainController());
