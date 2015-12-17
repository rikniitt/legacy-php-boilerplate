<?php

namespace Web\Middleware;

use Web\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class TokenAuthentication
{

    public function before(Request $request, Application $app)
    {
        if (!$request->headers->get('X-Authentication-Token')) {
            $app->abort(401, 'Not authenticated. Header X-Authentication-Token missing.');
        }

        $token = $request->headers->get('X-Authentication-Token');

        if (!$this->isAuthorized($token, $app)) {
            $app->abort(401, 'Not authenticated. X-Authentication-Token ' . $token . ' is not authorized.');
        }
    }

    private function isAuthorized($token, $app)
    {
        if ($app->getSetting('AUTHORIZED_TOKENS')) {
            $authorizedTokens = explode(',', $app->getSetting('AUTHORIZED_TOKENS'));
            return in_array($token, $authorizedTokens);
        } else {
            $app['monolog']->error('AUTHORIZED_TOKENS is not set. Will deny all requests.');
            return false;
        }
    }

}
