<?php
namespace BoardBerry\Games\Alias\Routing;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

            return new JsonResponse([]);
        });

        $collection->match('room/{id}', function ($id)
        {
            return 'room id ' . $id;
        });

        return $collection;
    }
}