<?php

namespace Legacy\Library\Silex;

use Legacy\Application;
use Silex\CallbackResolver as SilexCallbackResolver;
use InvalidArgumentException;

/**
 * Custom route callback resolver.
 *
 * Logic, which calls on these methods, is on Silex\ServiceControllerResolver.
 * We modify the built in CallbackResolver so that it can load Controller@method
 * route definitions by using the custom "resolve"-method on our $app.
 */
class CallbackResolver extends SilexCallbackResolver
{
    // Parent isValid method references this with static::SERVICE_PATTERN
    const SERVICE_PATTERN = "/[A-Za-z0-9\._\-]+@[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

    private $app;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function convertCallback($name)
    {
        list($service, $method) = explode('@', $name, 2);

        try {
            $resolved = $this->app->resolve($service);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        return [$resolved, $method];
    }

}
