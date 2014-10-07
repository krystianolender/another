<?php

Loader::loadValidate('Interface');
abstract class Validate implements InterfaceValidate {
    protected $name;
    protected $rule;
    protected $errorMessage;

    /**
     * Ustawia regułę dla walidatora
     *
     * @param string $rule - reguła w formie wyrażenia regularnego
     */
    public function setRule($rule) {
        $this->rule = $rule;
    }

    /**
     * Pobiera regułę dla walidatora
     *
     * @return string reguła w formie wyrażenia regularnego
     */
    public function getRule() {

        return $this->rule;
    }

    /**
     * Pobiera wiadomość błędu dla walidatora
     *
     * @return string wiadomość błędu dla walidatora
     */
    public function getErrorMessage() {

        return $this->errorMessage;
    }

    /**
     * Pobiera nazwę walidatora
     *
     * @return string nazwa walidatora
     */
    public function getName() {

        return $this->name;
    }

    protected function getFormData() {
        Loader::loadComponent('Request');
        $requestComponent = new RequestComponent();

        if($requestComponent->isPost()) {
            $formData = $requestComponent->getPost();
        } else {
            $formData = $requestComponent->getGet();
        }

        return $formData;
    }
}