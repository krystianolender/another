<?php

class UserRegisterForm extends FormComponent {
    public function __construct($name = 'User') {
        parent::__construct($name);
        $this->create();

        Loader::loadValidate('NotNull');
        $notNullValidator = new NotNullValidate();
        Loader::loadValidate('Unique');
        $uniqueValidator = new UniqueValidate();
        Loader::loadValidate('TheSamePassword');
        $theSamePasswordValidator = new TheSamePasswordValidate();

        $this->addUsernameElement(array($notNullValidator, $uniqueValidator));
        $this->addPasswordElement(array($notNullValidator));
        $this->addPasswordConfirmationElement(array($notNullValidator, $theSamePasswordValidator));
        $this->addFirstNameElement(array($notNullValidator));
        $this->addLastNameElement(array($notNullValidator));
        $this->addSubmitElement();

        return $this;
    }

    private function create() {
        Loader::loadComponent('FormFilter');
        $this->setMethod(RequestComponent::POST);
        $this->setAddress('User', 'registerAction');
    }

    private function addUsernameElement($validators) {
        $usernameElement = new FormElementComponent('username');
        $usernameElement->addFilter(FormFilterComponent::TRIM);

        foreach ($validators as $validator) {
            $usernameElement->addValidator($validator);
        }
        $this->addElement($usernameElement);
    }

    private function addPasswordElement($validators) {
        $passwordElement = new FormElementComponent('password', 'password');
        $passwordElement->addFilter(FormFilterComponent::TRIM);
        foreach ($validators as $validator) {
            $passwordElement->addValidator($validator);
        }

        $this->addElement($passwordElement);
    }

    private function addPasswordConfirmationElement($validators) {
        $passwordElement = new FormElementComponent('password_confirmation', 'password');
        $passwordElement->addFilter(FormFilterComponent::TRIM);
        foreach ($validators as $validator) {
            $passwordElement->addValidator($validator);
        }

        $this->addElement($passwordElement);
    }

    public function addFirstNameElement($validators) {
        $firstNameElement = new FormElementComponent('first_name');
        $firstNameElement->addFilter(FormFilterComponent::TRIM);
        foreach ($validators as $validator) {
            $firstNameElement->addValidator($validator);
        }

        $this->addElement($firstNameElement);
    }

    public function addLastNameElement($validators) {
        $lastNameElement = new FormElementComponent('last_name');
        $lastNameElement->addFilter(FormFilterComponent::TRIM);
        foreach ($validators as $validator) {
            $lastNameElement->addValidator($validator);
        }

        $this->addElement($lastNameElement);
    }

    public function addSubmitElement() {
        $submitName = 'Wyslij';
        $submitElement = new FormElementComponent($submitName, 'submit');
        $submitElement->setValue($submitName);
        $this->addElement($submitElement);
    }
}