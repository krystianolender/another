<?php

class UserEditForm extends FormComponent {
    public function __construct($name = 'User') {
        parent::__construct($name);
        $this->create();

        Loader::loadValidate('NotNull');
        $notNullValidator = new NotNullValidate();
        Loader::loadValidate('TheSamePassword');
        $theSamePasswordValidator = new TheSamePasswordValidate();

        $this->addIdElement();
        $this->addUsernameElement($notNullValidator);
        $this->addPasswordElement($notNullValidator);
        $this->addPasswordConfirmationElement(array($notNullValidator, $theSamePasswordValidator));
        $this->addFirstNameElement($notNullValidator);
        $this->addLastNameElement($notNullValidator);
        $this->addSubmitElement();

        return $this;
    }

    private function create() {
        Loader::loadComponent('FormFilter');
        $this->setMethod(RequestComponent::POST);
        $this->setAddress('User', 'editAction');
    }

    private function addIdElement() {
        $idElement = new FormElementComponent('id', FormElementComponent::HIDDEN_TYPE);
        $this->addElement($idElement);
    }

    private function addUsernameElement($validator) {
        $usernameElement = new FormElementComponent('username');
        $usernameElement->addFilter(FormFilterComponent::TRIM);

        $usernameElement->addValidator($validator);
        $this->addElement($usernameElement);
    }

    private function addPasswordElement($validator) {
        $passwordElement = new FormElementComponent('password', FormElementComponent::PASSWORD_TYPE);
        $passwordElement->addValidator($validator);

        $this->addElement($passwordElement);
    }

    private function addPasswordConfirmationElement($validators) {
        $passwordElement = new FormElementComponent('password_confirmation', FormElementComponent::PASSWORD_TYPE);
        foreach ($validators as $validator) {
            $passwordElement->addValidator($validator);
        }

        $this->addElement($passwordElement);
    }

    public function addFirstNameElement($validator) {
        $firstNameElement = new FormElementComponent('first_name');
        $firstNameElement->addValidator($validator);
        $this->addElement($firstNameElement);
    }

    public function addLastNameElement($validator) {
        $lastNameElement = new FormElementComponent('last_name');
        $lastNameElement->addValidator($validator);
        $this->addElement($lastNameElement);
    }

    public function addSubmitElement() {
        $submitName = 'Wyslij';
        $submitElement = new FormElementComponent($submitName, 'submit');
        $submitElement->setValue($submitName);
        $this->addElement($submitElement);
    }
}