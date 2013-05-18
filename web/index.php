<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = 1;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->get('/', function () use ($app) {
    return $app['twig']->render('layout.twig');
});


$app->get('/alias', function () use ($app) {
    return $app['twig']->render('alias/index.twig');
});


$app->run();