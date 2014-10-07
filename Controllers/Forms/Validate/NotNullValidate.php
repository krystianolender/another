<?php

Loader::loadValidate();
class NotNullValidate extends Validate {
    protected $name = 'notNull';
    protected $rule = '/^\s*$/';
    protected $errorMessage = 'Pole nie może być puste!';

    public function valid($value, $modelName, $inputName) {
        if(!preg_match($this->rule, $value)) {
            $isValid = true;
        } else {
            $isValid = false;
        }

        return $isValid;
    }
}