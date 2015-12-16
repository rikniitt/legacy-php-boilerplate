<?php

namespace Web\Database;

use Web\Application;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

abstract class Repository extends EntityRepository
{
    // Which model/entity.
    protected $modelName = 'Web\Database\Model\NotDefined';

    // Which connection.
    protected $entityManager = 'orm.em';

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $entityManager = $app[$this->entityManager];
        $clazz = new ClassMetadata($this->modelName);
        parent::__construct($entityManager, $clazz);
    }

}
