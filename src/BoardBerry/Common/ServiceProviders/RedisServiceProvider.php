<?php
use Silex\Application;

/**
 * Created by JetBrains PhpStorm.
 * User: vitkovskii
 * Date: 5/18/13
 * Time: 3:54 PM
 * To change this template use File | Settings | File Templates.
 */

class RedisServiceProvider implements \Silex\ServiceProviderInterface{

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
        // TODO: Implement register() method.
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