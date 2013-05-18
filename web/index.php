<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = 1;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$commonRouting = new \BoardBerry\Common\CommonRouting('');
$app->mount('', $commonRouting);

$commonApiRouting = new \BoardBerry\Common\CommonApiRouting('');
$app->mount('', $commonApiRouting);

$apiRouting = new \BoardBerry\Games\Alias\ApiRouting('');
$app->mount('', $apiRouting);

$app->run();