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

    public function asDebugArray()
    {
        $data = (array) $this;
        $arra = array();

        /**
         * Type casting to array causes private member names
         * to be prepended with null character wrapped object
         * class path.
         *   e.g. '\0Your\Namespace\Model\Name\0attributeName'
         *
         * http://php.net/manual/en/language.types.array.php#language.types.array.casting
         *
         * Try to clean property names.
         */
        foreach ($data as $key => $val) {
            $k = substr($key, strrpos($key, "\0") + 1);
            $arra[$k] = $val;
        }

        return $arra;
    }

}
