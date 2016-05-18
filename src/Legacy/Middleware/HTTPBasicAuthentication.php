<?php

namespace Legacy\Middleware;

use Legacy\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HTTPBasicAuthentication
{
    /**
     * Authorized users array
     *  where username => (hashed) password.
     *
     * To add new user:
     * $ ./app_console
     * >>> Legacy\Middleware\HTTPBasicAuthentication::hashPassword('new super sweet password');
     * Copy-paste result to $authorizedUsers array.
     *
     * @var array
     */
    private $authorizedUsers = array(
        'admin' => '$1$PJotY6fY$Sk.eRD/LEhAKHWXAMUutu/'
    );
    
    private $realm = 'Secure Realm';

    public function before(Request $request, Application $app)
    {
        $username = $request->headers->get('PHP_AUTH_USER') ? : null;
        $password = $request->headers->get('PHP_AUTH_PW') ? : null;
        
        if (!$this->verify($username, $password)) {
            $response = new Response();
            $response->setStatusCode(401)
                     ->setContent('Not authorized!')
                     ->headers->set('WWW-Authenticate', 'Basic realm="' . $this->realm . '"');
            return $response;
        }

    }

    private function verify($username, $password)
    {
        $users = $this->authorizedUsers;

        return (bool) ($username
                       && $password
                       && is_array($users)
                       && array_key_exists($username, $users)
                       && $users[$username] === static::hashPassword($password, $users[$username]));
    }

    public static function hashPassword($password, $hash = false)
    {
        return ($hash === false) ? crypt($password) : crypt($password, $hash);
    }

}
