<?php

namespace Legacy\Controller;

use Legacy\Controller;
use Legacy\Application;
use Legacy\Database\Repository;
use Symfony\Component\HttpFoundation\Request;

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

    public function show($id)
    {
        $todo = $this->repository->find($id);

        if ($todo) {
            return $this->render('todo/show.twig', array(
                'todo' => $todo
            ));
        } else {
            $this->app->abort(404, sprintf('Could not find todo with id %d.', $id));
        }
    }

    public function delete($id)
    {
        $todo = $this->repository->find($id);

        if ($todo) {
            $this->repository->delete($todo);
            return $this->app->redirect('/todo');
        } else {
            return $this->app->abort(404, sprintf('Could not find todo with id %d.', $id));
        }
    }

    public function create()
    {
        return $this->render('todo/form.twig', array(
            'todo' => $this->repository->create(),
            'formAction' => $this->url('/todo/save')
        ));
    }

    public function save(Request $request)
    {
        $todo = $this->repository->create();

        $todo->setName($request->get('name'));
        $todo->setContent($request->get('content'));

        if ($this->repository->save($todo)) {
            return $this->app->redirect('/todo/' . $todo->getId());
        } else {
            // Report errors to user with $todo->getValidationErrors()
            return $this->app->redirect('/todo/create');
        }
    }

    public function edit($id)
    {
        $todo = $this->repository->find($id);

        if ($todo) {
            return $this->render('todo/form.twig', array(
                'todo' => $todo,
                'formAction' => $this->url('/todo/update/' . $todo->getId())
            ));
        } else {
            $this->app->abort(404, sprintf('Could not find todo with id %d.', $id));
        }

    }

    public function update($id, Request $request)
    {
        $todo = $this->repository->find($id);

        if (!$todo) {
            $this->app->abort(404, sprintf('Could not find todo with id %d.', $id));
        }

        $todo->setName($request->get('name'));
        $todo->setContent($request->get('content'));

        if ($this->repository->update($todo)) {
            return $this->app->redirect('/todo/' . $todo->getId());
        } else {
            // Report errors to user with $todo->getValidationErrors()
            return $this->app->redirect('/todo/edit/' . $todo->getId());
        }
    }

}
