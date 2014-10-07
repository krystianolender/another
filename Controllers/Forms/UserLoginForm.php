<?php

class UserLoginForm extends FormComponent {
    public function __construct($name = 'User') {
        parent::__construct($name);

        Loader::loadComponent('FormFilter');

        $this->setMethod(RequestComponent::POST);
        $this->setAddress('User', 'loginAction');

        $loginFormUsernameElement = new FormElementComponent('username');
        $loginFormUsernameElement->addFilter(FormFilterComponent::TRIM);

        Loader::loadValidate('NotNull');
        $notNullValidator = new NotNullValidate();
        $loginFormUsernameElement->addValidator($notNullValidator);
        $this->AddElement($loginFormUsernameElement);

        $loginFormPasswordElement = new FormElementComponent('password', 'password');
        $this->AddElement($loginFormPasswordElement);

        $submitName = 'Wyslij';
        $loginFormSubmitElement = new FormElementComponent($submitName, 'submit');
        $loginFormSubmitElement->setValue($submitName);
        $this->AddElement($loginFormSubmitElement);

        return $this;
    }
}