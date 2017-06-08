<?php

namespace Legacy\Library\Alerts;

class Container
{

    protected $alerts;

    protected $levels = [
        'success',
        'info',
        'warning',
        'danger'
    ];

    public function __construct()
    {
        $this->alerts = [];
    }

    public function getAll()
    {
        return $this->alerts;
    }

    public function add($message, $level = 'info')
    {
        if (!in_array($level, $this->levels)) {
            $level = 'info';
        }

        $this->alerts[] = [
            'level' => $level,
            'message' => $message
        ];
    }

}
