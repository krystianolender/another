<?php

Loader::loadValidate();
class TheSamePasswordValidate extends Validate {
    protected $name = 'theSameValues';
    protected $rule = false;
    protected $errorMessage = 'Wartości pól z hasłem nie są identyczne';

    public function valid($value, $modelName, $inputName) {
        $formData = $this->getFormData();

        $password = trim($formData['password']);
        $passwordConfirmation = trim($formData['password_confirmation']);

        if(($password === $passwordConfirmation)) {

            return true;
        } else {

            return false;
        }
    }
}