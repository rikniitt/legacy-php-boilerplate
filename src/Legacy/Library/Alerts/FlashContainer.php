<?php

namespace Legacy\Library\Alerts;

use Symfony\Component\HttpFoundation\Session\SessionInterface as Session;

class FlashContainer extends Container
{

    private $session;

    const SESSION_KEY = 'flashed-alerts';

    public function __construct(Session $session)
    {
        parent::__construct();
        $this->session = $session;
    }

    public function add($message, $level = 'info')
    {
        parent::add($message, $level);

        $this->session->set(self::SESSION_KEY, serialize($this->alerts));
    }

    public function copyToOther(Container $other)
    {
        if ($this->session->has(self::SESSION_KEY)) {
            $data = unserialize($this->session->get(self::SESSION_KEY));
            if (is_array($data)) {
                foreach ($data as $alert) {
                    $other->add($alert['message'], $alert['level']);
                }
            }
        }
    }

    public function reset()
    {
        $this->session->remove(self::SESSION_KEY);
    }

}
