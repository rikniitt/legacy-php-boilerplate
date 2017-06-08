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

}
