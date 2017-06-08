<?php

namespace Legacy\Middleware;

use Legacy\Application;
use Symfony\Component\HttpFoundation\Request;

class IPWhitelist
{
    private $ipWhitelist = [
        '127.0.0.1'
    ];

    public function before(Request $request, Application $app)
    {
        $ip = $request->getClientIp();

        if ($this->isNotWhitelisted($ip)) {
             $app->abort(403, 'Forbidden! '.$ip);
        }
    }

    private function isNotWhitelisted($ipAddress)
    {
        return !in_array($ipAddress, $this->ipWhitelist);
    }

}
