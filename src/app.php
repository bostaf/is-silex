<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliński <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Debug\ErrorHandler;
use Silex\Provider;

// Session
$app->register(new Silex\Provider\SessionServiceProvider());

// Service provider
$app->register(new Rpodwika\Silex\YamlConfigServiceProvider(__DIR__.'/../app/config/config.yml'));
$app->register(new Rpodwika\Silex\YamlConfigServiceProvider(__DIR__.'/../app/config/members.yml'));
$app['debug'] = $app['config']['app']['debug'];
$app['env'] = $app['config']['app']['env'];

//register an error handler
// todo implement error handling for different error/exception categories (currently there's one common)
ErrorHandler::register();
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }
    //return your json response here
    $error = array('message' => $e->getMessage(), '1' => $e->getLine(), '2' => $e->getFile());

    return $app['twig']->render('_error.html.twig', []);
});

// Twig templating
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/views',
));

$app->extend('twig', function($twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Intl());
    return $twig;
});

$app->register(new Silex\Provider\HttpFragmentServiceProvider());

// Asset service provider
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v3',
    'assets.version_format' => '%s?version=%s',
    'assets.named_packages' => array(
        'css' => array('base_path' => '/css', 'version' => 'v1'),
        'js' => array('base_path' => '/js', 'version' => 'v1'),
        'img' => array('base_path' => '/img', 'version' => 'v1'),
    ),
));

// Controller
$app->mount('/', new Is\Controller\MainController());

// Security
$securityFirewalls = array(
    'main' => array(
        'pattern' => '^/',
        'anonymous' => true,
        'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
        'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
        'users' => function() use ($app) {
            return new \Is\Security\User\UserProvider($app['config']['data']['users']['file']);
        }
    )
);
$securityRoleHierarchy = array(
    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH', 'ROLE_MOD'),
    'ROLE_MOD' => array('ROLE_USER'),
);
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => $securityFirewalls,
    'security.role_hierarchy' => $securityRoleHierarchy,
    'security.default_encoder' => function($app) {
        return new Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(10);
    },
    'security.access_rules' => array(
        array('^/user/password', 'ROLE_USER')
    )
));

// Profiler
if ($app['debug']) {
    $app->register(new Silex\Provider\ServiceControllerServiceProvider());
    $app->register(new Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../cache/profiler',
        'profiler.mount_prefix' => '/_profiler', // this is the default
    ));
}
$app->boot();
