<?php

namespace Legacy\Middleware;

use Symfony\Component\HttpFoundation\Request;

class JSONRequest
{

    public function before(Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
    }

}
