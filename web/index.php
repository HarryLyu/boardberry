<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../lib/Realplexor.php';

$app = new Silex\Application();
$app['debug'] = 1;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->register(new \BoardBerry\Common\ServiceProviders\RedisServiceProvider());
$app->register(new \BoardBerry\Common\ServiceProviders\UserServiceProvider());
$app->register(new \BoardBerry\Common\ServiceProviders\CometServiceProvider());

$app->register(new \BoardBerry\Games\Alias\ServiceProviders\RoomServiceProvider());

$commonRouting = new \BoardBerry\Common\Routing\CommonRouting('');
$app->mount('', $commonRouting);

$commonApiRouting = new \BoardBerry\Common\Routing\CommonApiRouting('');
$app->mount('api', $commonApiRouting);

$apiRouting = new \BoardBerry\Games\Alias\Routing\ApiRouting('');
$app->mount('api', $apiRouting);


$app->get('/alias', function () use ($app) {
    return $app['twig']->render('alias/index.twig', array(
        'host' => $_SERVER['HTTP_HOST']
    ));
});


$app->run();