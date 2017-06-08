<?php

namespace Legacy\Middleware;

/**
 * Tries to do same as setting
 * expose_php = Off in php.ini.
 * Note, that ini_set() for
 * this setting wont work.
 */
class PhpExposeOff
{

    public function after()
    {
        header_remove('X-Powered-By');
    }

}
