<?php

namespace Legacy\Database;

use Legacy\Application;
use Legacy\Database\Model;
use Legacy\Library\Pagination;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class Repository extends EntityRepository
{
    // Which model/entity.
    protected $modelName = 'Legacy\Database\Model\NotDefined';

    // Which connection.
    protected $entityManager = 'not-defined';

    // Part of error message in convert method.
    protected $entityNotFoundName = 'Entity';

    // List of field names to match search
    protected $searchFields = [];

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $entityManager = $app['orm.ems'][$this->entityManager];
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

    public function freeMemory()
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function getCount(Criteria $criteria = null)
    {
        $query = $this->createQueryBuilder('model')
                      ->select('COUNT(model)');

        if ($criteria) {
            $query->addCriteria($criteria);
        }

        $count = $query->getQuery()
                       ->getSingleScalarResult();

        return intval($count);
    }

    public function getIdentifierField()
    {
        return $this->getEntityManager()
                    ->getClassMetadata($this->modelName)
                    ->getSingleIdentifierFieldName();
    }

    public function hasField($field)
    {
        return $this->getEntityManager()
                    ->getClassMetadata($this->modelName)
                    ->hasField($field);
    }

    public function findAllPaginated(Pagination $parameters, Criteria $criteria = null)
    {
        if (!$criteria) {
            $criteria = Criteria::create();
        }

        if ($parameters->q() !== '' && count($this->searchFields) > 0) {
            $likeExpressions = [];
            foreach ($this->searchFields as $field) {
                $search = addcslashes(mb_strtolower($parameters->q()), '%_');
                $likeExpressions[] = $criteria->expr()->contains($field, $search);
            }

            $criteria->andWhere(
                new CompositeExpression(
                    CompositeExpression::TYPE_OR,
                    $likeExpressions
                )
            );
        }

        $parameters->setTotalCount($this->getCount($criteria));

        if ($this->hasField($parameters->sort())) {
            $field = $parameters->sort();
        } else {
            $field = $this->getIdentifierField();
        }

        $order = $parameters->order();
        $limit = $parameters->limit();
        $offset = ($parameters->page() - 1) * $limit;

        $criteria->orderBy([$field => $order])
                 ->setFirstResult($offset)
                 ->setMaxResults($limit);

        $data = $this->matching($criteria);
        $parameters->setData($data);

        return $data;
    }

    public function convert($id)
    {
        $entity = $this->find($id);

        if (!$entity) {
            $message = sprintf(
                '%s #%s was not found,',
                $this->entityNotFoundName,
                $id
            );

            throw new NotFoundHttpException($message);
        }

        return $entity;
    }

    /**
     * Do "plain" SQL insert.
     *
     * Execute insert in database which $entityManager
     * is connected to and table which $modelName maps to.
     *
     * Note that this by passes validation and the ORM.
     * Keys of $data array must be existing column names
     * on the database table.
     *
     * @param array $data Example ['name' => 'John doe', 'age' => 45]
     * @return integer The number of affected rows.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function plainInsert(array $data)
    {
        $em = $this->getEntityManager();

        $meta = $em->getClassMetadata($this->modelName);
        $table = $meta->getTableName();

        // Creates array like 'key' => ':key'
        $values = array_combine(
            array_keys($data),
            array_map(function($key) {
                return ':' . $key;
            }, array_keys($data))
        );

        // Use Doctrine\DBAL\Query\QueryBuilder not orm
        $qb = $em->getConnection()
                 ->createQueryBuilder();

        return $qb->insert($table)
                  ->values($values)
                  ->setParameters($data)
                  ->execute();
    }

}
