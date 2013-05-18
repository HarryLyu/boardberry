<?php
namespace BoardBerry\Games\Alias\ServiceProviders;

use BoardBerry\Games\Alias\Game\Room\RoomIdGenerator;
use BoardBerry\Games\Alias\Game\Room\RoomManager;
use Silex\Application;
use Silex\ServiceProviderInterface;

class RoomServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['alias.room-id-generator'] = \Pimple::share(function () use ($app) {
            return new RoomIdGenerator($app['redis']);
        });

        $app['alias.room-manager'] = \Pimple::share(function () use ($app) {
            return new RoomManager($app['redis'], $app['alias.room-id-generator']);
        });

    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registers
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}