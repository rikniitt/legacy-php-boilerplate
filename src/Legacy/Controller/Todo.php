<?php

namespace Legacy\Controller;

use Legacy\Controller;
use Legacy\Application;
use Legacy\Database\Repository;

class Todo extends Controller
{
    private $repository;

    public function __construct(Application $app, Repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->render('todo/index.twig', array(
            'todos' => $this->repository->findAll()
        ));
    }

}
