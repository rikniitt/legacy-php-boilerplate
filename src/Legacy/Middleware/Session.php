<?php

namespace Legacy\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Legacy\Application;

class Session
{

    public function before(Request $request, Application $app)
    {
        $request->getSession()->start();

        $flashed = $app['flash.alerts'];
        $alerts = $app['alerts'];

        $flashed->copyToOther($alerts);
        $flashed->reset();
    }

}
