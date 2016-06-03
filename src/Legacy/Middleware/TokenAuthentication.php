<?php

namespace Legacy\Middleware;

use Legacy\Application;
use Symfony\Component\HttpFoundation\Request;

class TokenAuthentication
{

    public function before(Request $request, Application $app)
    {
        if (!$request->headers->get('X-Authentication-Token')) {
            $app->abort(401, 'Not authenticated. Header X-Authentication-Token missing.');
        }

        $token = $request->headers->get('X-Authentication-Token');

        if (!$this->isAuthorized($token, $app)) {
            $msg = sprinf(
                'Not authenticated. X-Authentication-Token %s is not authorized.',
                $token
            );
            $app->abort(401, $msg);
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
