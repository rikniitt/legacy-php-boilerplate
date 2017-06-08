<?php

namespace Legacy\Middleware;

use Legacy\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provide access to current request via application
 * like in silex 1.3 via $app['request'].
 */
class BindCurrentRequestToApplication
{

    public function before(Request $request, Application $app)
    {
        $app['current.request'] = $request;
    }

}
