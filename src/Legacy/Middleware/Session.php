<?php

namespace Legacy\Middleware;

use Symfony\Component\HttpFoundation\Request;

class TokenAuthentication
{

    public function before(Request $request)
    {
        $request->getSession()->start();
    }

}
