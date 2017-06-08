<?php

namespace Legacy\Library;

class Pagination
{

    private $usePagination;
    private $page;
    private $limit;
    private $sort;
    private $order;
    private $data;
    private $totalCount;
    private $defaults = [
        'page' => 1,
        'limit' => 25,
        'sort' => '',
        'order' => 'ASC'
    ];
    private $wasInQuery;
    private $reqHelper;

    public function __construct(array $parameters, RequestHelper $reqHelper)
    {
        $this->reqHelper = $reqHelper;
        $this->usePagination = $this->containsAnyPaginationParamaters($parameters);

        $params = array_merge(
            $this->defaults,
            $parameters
        );

        $this->page = max(1, ((int) $params['page']));
        $this->limit = abs((int) $params['limit']);
        $this->sort = (string) $params['sort'];
        $this->order = strtoupper((string) $params['order']);

        if (!in_array($this->order, ['ASC', 'DESC'])) {
            $this->order = 'ASC';
        }

        // These gets set by repository.
        $this->data = [];
        $this->totalCount = -1;
    }

    private function containsAnyPaginationParamaters($parameters)
    {
        $findSome = false;
        $fields = array_keys($this->defaults);
        $this->wasInQuery = [];

        foreach ($fields as $key) {
            $keyExistInParameters = array_key_exists($key, $parameters);
            $findSome = $findSome || $keyExistInParameters;
            $this->wasInQuery[$key] = $keyExistInParameters;
        }

        return $findSome;
    }

    public function setDefaultSort($sort)
    {
        if (!$this->wasInQuery['sort']) {
            $this->sort = $sort;
        }

        return $this;
    }

    public function setDefaultOrder($order)
    {
        if (!$this->wasInQuery['order']) {
            $this->order = $order;
        }

        return $this;
    }

    /**
     * Did $parameters contain any of pagination params.
     */
    public function usePagination()
    {
        return $this->usePagination;
    }

    public function page()
    {
        return $this->page;
    }

    public function limit()
    {
        return $this->limit;
    }

    public function sort()
    {
        return $this->sort;
    }

    public function order()
    {
        return $this->order;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function data()
    {
        return $this->data;
    }

    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    public function totalCount()
    {
        return $this->totalCount;
    }

    public function fromEntry()
    {
        $from = ($this->page() - 1) * $this->limit() + 1;
        return min($from, $this->totalCount());
    }

    public function toEntry()
    {
        $to = $this->fromEntry() + $this->limit() - 1;
        return min($to, $this->totalCount());
    }

    public function hasPrev()
    {
        return ($this->page() > 1);
    }

    public function hasNext()
    {
        return ($this->toEntry() < $this->totalCount());
    }

    public function lastPageNumber()
    {
        return (int) ceil($this->totalCount() / $this->limit());
    }

    public function neighborPageNumbers()
    {
        $from = max(1, ($this->page() - 2));
        $to = min(($this->page() + 2), max(1, $this->lastPageNumber()));

        if ($from === $to) {
            return [];
        } else {
            return range($from, $to);
        }
    }

    public function prevPageUrl()
    {
        if ($this->hasPrev()) {
            return $this->reqHelper->currentUrl([
                'page' => $this->page() - 1,
                'limit' => $this->limit(),
                'sort' => $this->sort(),
                'order' => $this->order()
            ]);
        }

        return 'javascript:void(0)';
    }

    public function nextPageUrl()
    {
        if ($this->hasNext()) {
            return $this->reqHelper->currentUrl([
                'page' => $this->page() + 1,
                'limit' => $this->limit(),
                'sort' => $this->sort(),
                'order' => $this->order()
            ]);
        }

        return 'javascript:void(0)';
    }

    public function pageUrl($number)
    {
        if ($number == $this->page()) {
            return 'javascript:void(0)';
        } else {
            return $this->reqHelper->currentUrl([
                'page' => $number,
                'limit' => $this->limit(),
                'sort' => $this->sort(),
                'order' => $this->order()
            ]);
        }
    }

    public function asArray()
    {
        return [
            'usePagination' => $this->usePagination,
            'page' => $this->page,
            'limit' => $this->limit,
            'sort' => $this->sort,
            'order' => $this->order,
            'data' => $this->data,
            'totalCount' => $this->totalCount
        ];
    }

}
