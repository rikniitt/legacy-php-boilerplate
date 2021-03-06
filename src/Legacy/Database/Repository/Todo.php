<?php

namespace Legacy\Database\Repository;

use Legacy\Database\Repository;

class Todo extends Repository
{
    protected $modelName = 'Legacy\Database\Model\Todo';

    protected $entityManager = 'myWebApplication';

    protected $searchFields = ['name', 'content'];
}
