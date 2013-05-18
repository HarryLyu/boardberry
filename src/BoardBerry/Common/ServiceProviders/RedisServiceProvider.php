<?php
namespace BoardBerry\Common\ServiceProviders;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RedisServiceProvider implements ServiceProviderInterface{

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
        $app['redis'] = \Pimple::share(function(){
               $redis = new \Redis();
               $redis->connect(REDIS_HOST,REDIS_PORT);
               return $redis;
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