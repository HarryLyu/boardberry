<?php
namespace BoardBerry\Common\Routing;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommonApiRouting implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $collection */
        $collection = $app['controllers_factory'];

        $collection->match('/user', function () use ($app) {
            return new JsonResponse(['error' => 'ok']);
        });

        return $collection;
    }
}