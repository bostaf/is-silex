<?php
use Symfony\Component\Debug\ErrorHandler;

$app->register(new Rpodwika\Silex\YamlConfigServiceProvider(__DIR__.'/../app/config/config.yml'));

ErrorHandler::register();
//register an error handler
$app->error(function ( \Exception $e, $code ) use ($app) {

    //return your json response here
    $error = array( 'message' => $e->getMessage() );

    return $app->json( $error, 200 );
});

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/views',
));

$app['debug'] = $app['config']['app']['debug'];

// Controller
$app->mount('/', new Is\Controller\MainController());
