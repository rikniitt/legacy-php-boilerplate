<?php

namespace Legacy\Database;

abstract class Model
{

    private $validationErrors = array();

    public function isValid()
    {
         $this->validationErrors = array();

         $this->validate();

         return (count($this->validationErrors) === 0);
    }

    abstract protected function validate();

    protected function addValidationError($message)
    {
        $this->validationErrors[] = $message;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

}
