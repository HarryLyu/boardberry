<?php
namespace BoardBerry\Common;

use Silex\Application;
use Silex\ControllerProviderInterface;

class CommonApiRouting implements ControllerProviderInterface {
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

        $collection->get('/user', function () use ($app) {
            return 'user registered!';
        });

        return $collection;
    }
}