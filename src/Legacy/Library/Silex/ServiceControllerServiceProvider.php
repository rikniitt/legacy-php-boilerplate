<?php

namespace Legacy\Library\Silex;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\ServiceControllerResolver;

class ServiceControllerServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app->extend('callback_resolver', function() use ($app) {
            return new CallbackResolver($app);
        });

        $app->extend('resolver', function ($controllerResolver, $app) {
            return new ServiceControllerResolver(
                $controllerResolver,
                $app['callback_resolver']
            );
        });
    }

}
