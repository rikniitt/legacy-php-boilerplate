<?php

namespace Legacy\Middleware;

use Symfony\Component\HttpFoundation\Request;

class Session
{

    public function before(Request $request)
    {
        $request->getSession()->start();
    }

}
