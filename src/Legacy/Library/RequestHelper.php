<?php

namespace Legacy\Library;

use Symfony\Component\HttpFoundation\Request;

class RequestHelper
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function url($path = '')
    {
        return $this->baseUrl() . $path;
    }

    /**
     * Return base url of request with trailing slash.
     *
     * @return string base url.
     */
    public function baseUrl()
    {
        $base = $this->request->getScheme() . '://' . $this->request->getHttpHost() . '/' . $this->request->getBaseUrl();
        return (substr($base, -1) === '/') ? $base : $base . '/';
    }

}