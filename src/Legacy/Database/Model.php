<?php

namespace Legacy\Database;

use Valitron\Validator;

abstract class Model
{

    private $validationErrors = [];

    public function isValid()
    {
        $this->validationErrors = [];
        $data = [];

        // This wont work if child has declared properties as private.
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        $validator = new Validator($data);
        $this->setValidationRules($validator);

        if ($validator->validate()) {
            return true;
        } else {
            $this->validationErrors = $validator->errors();
            return false;
        }
    }

    abstract protected function setValidationRules(Validator $validator);

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public function asDebugArray()
    {
        $data = (array) $this;
        $arra = [];

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
