<?php

namespace Legacy\Database;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationExceptionInterface as ValidationErrors;

abstract class Model
{

    private $validationErrors = array();

    public function isValid()
    {
        $this->validationErrors = array();

        $validator = new Validator();
        $this->setValidationRules($validator);

        $isValid = false;
        try {
            $isValid = $validator->assert($this);
        } catch (ValidationErrors $ex) {
            $this->addValidationError($ex->getFullMessage());
        }

        return ($isValid === true && count($this->validationErrors) === 0);
    }

    abstract protected function setValidationRules($validator);

    protected function addValidationError($message)
    {
        $this->validationErrors[] = $message;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

}
