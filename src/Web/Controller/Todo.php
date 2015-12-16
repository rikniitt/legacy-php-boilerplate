<?php

namespace Web\Controller;

use Web\Controller;
use Web\Application;
use Web\Database\Repository;

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
