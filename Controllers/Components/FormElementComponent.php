<?php
/**
 * Klasa elementów formularza.
 */
class FormElementComponent {
    private $name;
    private $id;
    private $type;
    private $lable;
    private $value;
    private $modelName;

    private $filters = array();
    private $validators = array();
    private $validatorErrors = array();

    const TEXT_TYPE = 'text';
    const PASSWORD_TYPE = 'password';
    const CHECKBOX_TYPE = 'checkbox';
    const SUBMIT_TYPE = 'submit';
    const HIDDEN_TYPE = 'hidden';

    public $allowedTypes = array(
        FormElementComponent::TEXT_TYPE,
        FormElementComponent::PASSWORD_TYPE,
        FormElementComponent::CHECKBOX_TYPE,
        FormElementComponent::SUBMIT_TYPE,
        FormElementComponent::HIDDEN_TYPE
    );

    public static function getInstance($name, $type = FormElementComponent::TEXT_TYPE, $value = null) {

        return new FormElementComponent($name, $type = FormElementComponent::TEXT_TYPE, $value = null);
    }

    public function __construct($name, $type = FormElementComponent::TEXT_TYPE, $value = null) {
        $this->setName($name);
        $this->setType($type);
        $this->setValue($value);
    }

    /**
     * Ustala nazwę elementu formularza
     *
     * @param string $name nazwa elementu formularza
     */
    public function setName($name) {
        $uncamelizeName = uncamelize($name);
        $this->name = $uncamelizeName;
    }

    /**
     * Pobiera nazwę elementu formularza
     *
     * @return string nazwa elementu formularza
     */
    public function getName() {

        return $this->name;
    }

    /**
     * Pobiera identyfikator elementu formularza
     *
     * @return identyfikator elementu formularza
     */
    public function getId() {

        return $this->id;
    }

    /**
     * Ustawia typ elementu formularza
     *
     * @param string $type - typ elementu formularza
     */
    public function setType($type) {
        if(in_array($type, $this->allowedTypes)) {
            $this->type = $type;
        } else {
            $this->type = FormElementComponent::TEXT_TYPE;
        }
    }

    /**
     * Pobiera typ elementu formularza
     *
     * @return string Typ elementu formularza
     */
    public function getType() {

        return $this->type;
    }

    /**
     * Ustawia etykietę dla elementu formularza
     *
     * @param string $lable - treść etykiety elementu formularza
     */
    public function setLable($lable) {
        $this->lable = $lable;
    }

    /**
     * Pobiera etykietę dla elementu formularza
     *
     * @return string treść etykiety elementu formularza
     */
    public function getLable() {

        return $this->lable;
    }

    /**
     * Ustawia wartość elementu formularza
     *
     * @param mixed $value - wartość elementa formularza
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Pobiera wartość elementu formularza
     *
     * @return mixed wartość elementu formularza
     */
    public function getValue() {

        return $this->value;
    }

    public function setModelName($modelName) {
        $this->modelName = $modelName;
    }

    public function getModelName() {

        return $this->modelName;
    }

    /**
     * Dodaje filtr do elementu formularza
     *
     * @param string $filterFormComponent - filtr
     */
    public function addFilter($filterFormComponent) {
        $this->filters[] = $filterFormComponent;
    }

    /**
     * Filtruje wartość przekazaną do elementu formularza
     * Na wartości elementu formularza wykonywane są wszystkie dodane filry
     */
    public function filter() {
        $formFilter = new FormFilterComponent($this->filters);
        $elementValue = $this->getValue();
        $elementFiltredValue = $formFilter->filter($elementValue);

        $this->setValue($elementFiltredValue);
    }

    /**
     * Dodaje walidator do listy walidatorów elementu formularza
     *
     * @param Validate $validator - dodawany walidator
     */
    public function addValidator(Validate $validator) {
        $this->validators[] = $validator;
    }

    /**
     * Sprawdza czy nie ma validatorów dla elementu formularza lub czy dane w elemencie formularza są poprawne pod kontem usawionych walidatorów
     *
     * @return true gdy nie ma walidatorów dla elementu formularza lub dane w elemencie formularza są poprawne
     */
    public function isValid() {
        if(!isset($this->validators) || empty($this->validators)) {
            $isValid = true;
        } else {
            $isValid = $this->checkAllValidators();
        }

        return $isValid;
    }

    /**
     * Sprawdza czy dane w elemencie formularza są poprawne pod kontem usawionych walidatorów
     *
     * @return true gdy dane w elemencie formularza są poprawne
     */
    private function checkAllValidators() {
        $this->setValidatorsErrors();
        $validationErrors = $this->getValidatorsErrors();
        if(!empty($validationErrors)) {

            return false;
        } else {

            return true;
        }
    }

    /**
     * Ustawia błędy walidatorów dla wartości znajdujących się w elemencie formularza
     */
    public function setValidatorsErrors() {
        $validatorErrors = array();
        foreach ($this->validators as $validator) {
            $validatorIsValid = $validator->valid($this->getValue(), $this->getModelName(), $this->name);
            if(!$validatorIsValid) {
                $validatorName = $validator->getName();
                $validatorMessage = $validator->getErrorMessage();
                $validatorErrors[$validatorName] = $validatorMessage;
            }
        }

        $this->validatorErrors = $validatorErrors;
    }

    /**
     * Pobiera błędy walidatorów dla wartości znajdujących się w elemencie formularza
     *
     * @return array błędy walidatorów dla wartości znajdujących się w elemencie formularza
     */
    public function getValidatorsErrors() {
        if(!isset($this->validatorErrors) || empty($this->validatorErrors)) {
            $this->setValidatorsErrors();
        }

        return $this->validatorErrors;
    }
}