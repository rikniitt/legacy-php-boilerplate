<?php

namespace Legacy\Database;

use Legacy\Application;
use Legacy\Database\Model;
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

    public function save(Model $entity)
    {
        if ($entity->isValid()) {
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
            return true;
        } else {
            return false;
        }
    }

    public function update(Model $entity)
    {
        if ($entity->isValid()) {
            $this->getEntityManager()->merge($entity);
            $this->getEntityManager()->flush();
            return true;
        } else {
            return false;
        }
    }

    public function delete(Model $entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function create()
    {
        $clazz = $this->getClassName();
        return new $clazz;
    }

}
