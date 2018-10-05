<?php

namespace Legacy\Middleware;

use Legacy\Application;
use Legacy\Library\RequestThrottle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Cache\FilesystemCache;

class RateLimiter
{

    public function before(Request $request, Application $app)
    {
        // This initialization should be done in config/services.php
        $lib = new RequestThrottle(
            new FilesystemCache(ROOT_DIR . '/cache/throttle'),
            $app->getSetting('RATE_REQUESTS', 50),
            $app->getSetting('RATE_INTERVAL', 180)
        );

        if ($lib->isThrottled($request)) {
            return new Response('Too many requests', 429);
        }
    }

}
