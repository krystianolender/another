<?php

Loader::loadValidate();
class UniqueValidate extends Validate {
    protected $name = 'unique';
    protected $rule = false;
    protected $errorMessage = 'Podana wartość nie jest dostępna';

    public function valid($value, $modelName, $inputName) {
        Loader::loadModel($modelName);
        $model = new $modelName;
        $valueWasUsed = $model->hasAnyBy($inputName, $value);
        if(!$valueWasUsed) {

            $isValid = false;
        } else {

            $isValid = true;
        }

        return $isValid;
    }
}