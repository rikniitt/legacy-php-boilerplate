<?php

namespace Legacy\Database;

use Iterator;
use Countable;
use ArrayAccess;

class RepositoryIterator implements Iterator, Countable, ArrayAccess
{
    private $repository;
    private $limit;
    private $offset;
    private $totalCount;
    private $currentOffset;

    private $where = array();
    private $orderBy = array();

    public function __construct(Repository $repository, $limit = null, $offset = 0)
    {
        $this->repository = $repository;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->totalCount = $repository->getCount();
        $this->currentOffset = $offset;
    }

    public function setWhere(array $where)
    {
        $this->where = $where;
    }

    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /* Iterator --> */

    public function current()
    {
        $this->repository->freeMemory();

        $result = $this->repository->findBy(
            $this->where,
            $this->orderBy,
            1,
            $this->currentOffset
        );

        return $result[0];
    }

    public function key()
    {
        return $this->currentOffset;
    }

    public function next()
    {
        $this->currentOffset = $this->currentOffset + 1;
    }

    public function rewind()
    {
        $this->currentOffset = $this->offset;
    }

    public function valid()
    {
        return ($this->currentOffset < $this->totalCount
                && (!$this->limit || $this->currentOffset - $this->offset < $this->limit));
    }

    /* Countable --> */

    public function count()
    {
        return $this->totalCount;
    }

    /* ArrayAccess --> */

    public function offsetExists($offset)
    {
        return (is_int($offset)
                && $offset >= $this->offset
                && $offset < $this->totalCount
                && (!$this->limit || $offset - $this->offset < $this->limit));
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \RuntimeException(
                'Invalid offset ' . $offset . ' in ' . get_class($this) . '.'
            );
        }

        // Use current() so that it always returns correct object
        $tempCurOffset = $this->currentOffset;
        $this->currentOffset = $offset;
        $item = $this->current();
        $this->currentOffset = $tempCurOffset;

        return $item;
    }

    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException(get_class($this) . ' is readonly.');
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException(get_class($this) . ' is readonly.');
    }

}
