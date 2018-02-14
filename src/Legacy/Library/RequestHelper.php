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
        return $this->baseUrl() . ltrim($path, '/');
    }

    /**
     * Return base url of request with trailing slash.
     *
     * @return string base url.
     */
    public function baseUrl()
    {
        $base = $this->request->getScheme() . '://'
                . $this->request->getHttpHost()
                . $this->request->getBaseUrl();
        return $this->addTrailingSlash($base);
    }

    private function addTrailingSlash($str)
    {
        return rtrim($str, '/') . '/';
    }

    public function currentUrl(array $params = array())
    {
        $req = $this->request;
        $url = $req->getSchemeAndHttpHost() . $req->getBaseUrl() . $req->getPathInfo();

        if ($req->getQueryString() !== null) {
            // Have to override existing query parameters with $params
            parse_str($req->getQueryString(), $oldParams);
            $params = array_merge($oldParams, $params);
        }

        return $url . '?' . http_build_query($params);
    }

    public function currentQueryParams()
    {
        return $this->request->query->all();
    }

}
