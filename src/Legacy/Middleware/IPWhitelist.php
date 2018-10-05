<?php

namespace Legacy\Middleware;

use Legacy\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class IPWhitelist
{
    private $ipWhitelist = array(
        '127.0.0.1',
        '192.168.0.0/24'
    );

    public function before(Request $request, Application $app)
    {
        $ip = $request->getClientIp();

        if ($this->isNotWhitelisted($ip)) {
             $app->abort(403, 'Forbidden! '.$ip);
        }
    }

    private function isNotWhitelisted($ipAddress)
    {
        return !IpUtils::checkIp($ipAddress, $this->ipWhitelist);
    }

}
