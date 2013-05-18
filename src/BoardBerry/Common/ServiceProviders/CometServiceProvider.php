<?php

namespace BoardBerry\Common\ServiceProviders;

use Silex\Application;
use Silex\ServiceProviderInterface;

class CometServiceProvider implements ServiceProviderInterface
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
        $app['comet'] = \Pimple::share(function () {
                $comet = new \Dklab_Realplexor(COMET_HOST, COMET_PORT, COMET_NAMESPACE);

                return $comet;
            }
        );
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