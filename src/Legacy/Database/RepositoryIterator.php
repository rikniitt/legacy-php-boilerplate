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
    private $count;
    private $totalCount;
    private $currentOffset;

    public function __construct(Repository $repository, $limit = null, $offset = 0)
    {
        $this->repository = $repository;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->currentOffset = $offset;
        $this->count = $this->getCount();
    }

    private function getCount()
    {
        $this->totalCount = $this->repository->getCount();

        if ($this->limit && $this->offset > 0) {
            return min($this->limit, $this->totalCount - $this->offset);
        } elseif ($this->limit) {
            return min($this->limit, $this->totalCount);
        } else {
            return $this->totalCount;
        }
    }

    /* Iterator --> */

    public function current()
    {
        $this->repository->freeMemory();

        $result = $this->repository->findBy(
            [],
            [],
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
        return $this->count;
    }

    /* ArrayAccess --> */

    public function offsetExists($offset)
    {
        if (!is_int($offset)) {
            return false;
        }

        return ($offset >= 0 && $offset < $this->count());
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
        $this->currentOffset = ($this->offset > 0) ? $this->offset + $offset : $offset;
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
