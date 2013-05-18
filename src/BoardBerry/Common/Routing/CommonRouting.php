<?php
namespace BoardBerry\Common\Routing;

use Silex\Application;
use Silex\ControllerProviderInterface;

class CommonRouting implements ControllerProviderInterface {
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $collection = $app['controllers_factory'];

        $collection->get('/', function () use ($app) {
            return $app['twig']->render('layout.twig');
        });

        return $collection;
    }
}