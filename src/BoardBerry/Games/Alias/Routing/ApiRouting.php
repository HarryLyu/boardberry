<?php
namespace BoardBerry\Games\Alias\Routing;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ApiRouting implements ControllerProviderInterface {
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

        $collection->match('room', function ()
        {
            return 'creating room!';
        });

        $collection->match('room/{id}', function ($id)
        {
            return 'room id ' . $id;
        });

        return $collection;
    }
}