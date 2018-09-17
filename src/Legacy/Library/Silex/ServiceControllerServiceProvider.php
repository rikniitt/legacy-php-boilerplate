<?php

namespace Legacy\Library\Silex;

use Silex\Application as SilexApplication;
use Silex\ServiceProviderInterface;
use Silex\ServiceControllerResolver;

class ServiceControllerServiceProvider implements ServiceProviderInterface
{

    public function register(SilexApplication $app)
    {
        $app['callback_resolver'] = $app->share(
            $app->extend('callback_resolver', function() use ($app) {
                return new CallbackResolver($app);
            })
        );

        $app['resolver'] = $app->share(
            $app->extend('resolver', function ($controllerResolver, $app) {
                return new ServiceControllerResolver(
                    $controllerResolver,
                    $app['callback_resolver']
                );
            })
        );
    }

    public function boot(SilexApplication $app)
    {
        // pass
    }

}
