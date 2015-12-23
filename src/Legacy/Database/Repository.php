<?php

namespace Legacy\Database;

use Legacy\Application;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

abstract class Repository extends EntityRepository
{
    // Which model/entity.
    protected $modelName = 'Legacy\Database\Model\NotDefined';

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

    public function save($entity)
    {
        $this->app[$this->entityManager]->persist($entity);
        $this->app[$this->entityManager]->flush();
    }

    public function update($entity)
    {
        $this->app[$this->entityManager]->merge($entity);
        $this->app[$this->entityManager]->flush();
    }

}
