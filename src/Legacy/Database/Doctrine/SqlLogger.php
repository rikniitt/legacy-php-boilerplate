<?php

namespace Legacy\Database\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger as DoctrineLoggingInterface;
use Monolog\Logger;

class SqlLogger implements DoctrineLoggingInterface
{

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->logger->info($sql, [$params, $types]);
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {

    }

}
