<?php
/**
 * Klasa pozwalająca zarządzać formularzami.
 *
 * Obsługuje i pozwala wpływać na działanie formularzy.
 * Zawygląd i wyświetlanie formularza odpowiedzialny jest FormHelper.
 * Elementy formularza są przechowywane w kolekcji obiektów klasy FormElementComponent
 */
Loader::loadComponent();
class FormComponent implements Component {
    private $method = 'post';

    private $controller;
    private $action;
    private $parameter;
    private $address;
    private $validatorsErrors = array();

    private $name = null;
    private $id = null;

    private $elements = array();

    public static function getInstance($name = null) {

        return new FormComponent($name);
    }

    public function __construct($name = null) {
        if($name) {
            $camelizeName = camelize($name);
            $this->name = $camelizeName;
        }

        Loader::loadComponent('FormElement');
    }

    /**
     * Ustala adres usupełniając go o domyślne wartości brakujących jego elementów
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @param mixed $parameter - parametr akcji
     */
    public function setAddress($controller = null, $action = null, $parameter = null) {
        $this->setAddressParts($controller, $action, $parameter);

        $this->address = Router::generateUrl($controller, $action, $parameter);
    }

    /**
     * Ustawia kontroler, akcje i parametr dla formularza
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @param mixed $parameter - parametr akcji
     */
    private function setAddressParts($controller = null, $action = null, $parameter = null) {
        $this->setController($controller);
        $this->setAction($action);
        $this->setParameter($parameter);
    }

    /**
     * Ustawia kontroler dla formularza.
     * Jeżeli kontroler nie jest podany używa aktualnego kontrolera
     *
     * @param string $controller - nazwa kontrolera
     */
    private function setController($controller = null) {
        if($controller) {
            $this->controller = $controller;
        } else {
            $this->controller = Router::getController();
        }
    }

    /**
     * Ustawia akcje dla formularza.
     * Jeżeli akcja nie jest podana używa aktualnej akcji
     *
     * @param string $action - nazwa akcji
     */
    private function setAction($action = null) {
        if($action) {
            $this->action = $action;
        } else {
            $this->action = Router::getAction();
        }
    }

    /**
     * Ustawia prametr dla formularza.
     * Jeżeli parametr nie jest podany używa aktualnego parametru
     *
     * @param mixed $parameter - parametr akcji
     */
    private function setParameter($parameter = null) {
        if($parameter) {
            $this->parameter = $parameter;
        } else {
            $this->parameter = Router::getParameter();
        }
    }

    /**
     * Ustawia metodę formularza (get/post)
     *
     * @param string $method - metoda formularza (get/post)
     */
    public function setMethod($method) {
        $allowedMethods = array(
            RequestComponent::GET,
            RequestComponent::POST
        );
        if(in_array($method, $allowedMethods)) {
            $this->method = $method;
        }
    }

    /**
     * Dodaje element do formularza
     *
     * @param FormElementComponent $formElement
     */
    public function addElement($formElement) {
        $formElementName = $formElement->getName();
        $formElement->setModelName($this->controller);
        $this->elements[$formElementName] = $formElement;
    }

    /**
     * Pobiera elementy formularza
     *
     * @return array FormElementComponent elementy formularza
     */
    public function getElements() {

        return $this->elements;
    }

    /**
     * Pobiera element formularza o podanej nazwie
     *
     * @param string $elementName - nazwa elementu
     * @return FormElementComponent
     */
    public function getElement($elementName) {
        if(isset($this->elements[$elementName])) {
            $element = $this->elements[$elementName];
        } else {
            $element = null;
        }

        return $element;
    }

    /**
     * Ustawia identyfikator formularza
     *
     * @param string $id identyfikator formularza
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Zwraca identyfikator formularza
     *
     * @return string identyfikator formularza
     */
    public function getId() {

        return $this->id;
    }

    /**
     * Zwraca metodę formularza (get/post)
     *
     * @return type
     */
    public function getMethod() {

        return $this->method;
    }

    /**
     * Zwraca adres formularza
     *
     * @return type
     */
    public function getAddress() {

        return $this->address;
    }

    /**
     *  Ustawia dane przesłane z formularza
     *
     */
    public function setData($data = null) {
        if(!$data) {
            $this->getDataFromRequest();
        } else {
            foreach ($this->elements as $element) {
                $elementName = $element->getName();
                if(isset($data->$elementName)) {
                    $element->setValue($data->$elementName);
                }
            }
        }
    }

    private function getDataFromRequest() {
        $requestComponent = new RequestComponent();
        if($this->getMethod() == RequestComponent::POST) {
            $dataFromRequest = $requestComponent->getPost();
        } else {
            $dataFromRequest = $requestComponent->getGet();
        }

        foreach ($this->elements as $element) {
            $elementName = $element->getName();
            if($requestComponent->check($elementName, $this->getMethod())) {
                $elementNewValue = $dataFromRequest[$elementName];
                $element->setValue($elementNewValue);
            }
        }
    }

    /**
     * Zwraca dane przesłane z formularza
     *
     * @param boolean $filtred - true jeżeli formularz ma być filtrowany
     * @return array dane przesłane z formularza
     */
    public function getData($filtred = true) {
        $this->setData();

        if($filtred) {
            $this->filterElements();
        }
        $dataFromForm = $this->getElementsValues();

        return $dataFromForm;
    }

    /**
     * Zwraca dane przesłane przez podany element formularza
     *
     * @param string $input - nazwa elementu formularza
     * @param boolean $filtred - true jeżeli formularz ma być filtrowany
     * @return mixed $inputData - dane przesłane przez element formularza
     */
    public function getInputData($input, $filtred = true) {
        $formData = $this->getData($filtred);

        if(isset($formData[$input])) {
            $inputData = $formData[$input];
        } else {
            $inputData = null;
        }

        return $inputData;
    }

    /**
     * Zwraca wartości elementów formularza
     *
     * @return array $elementsValues - tablica wartości elementów formularza
     */
    private function getElementsValues() {
        $elementsValues = array();
        foreach ($this->elements as $formElement) {
            $formElementName = $formElement->getName();
            $formElementValue = $formElement->getValue();
            $elementsValues[$formElementName] = $formElementValue;
        }

        return $elementsValues;
    }

    /**
     * Filtruje dane w elementach formularza
     */
    private function filterElements() {
        foreach ($this->elements as $formElement) {
            $formElement->filter();
        }
    }

    /**
     * Sprawdza czy dane w formularzu są poprawne
     * Dane poprawne to takie, dla których wszystkie walidatory elementu formularza zwracają true
     *
     * @return boolean true jeżeli wszystkie elementy formularza przeszły walidację
     */
    public function isValid() {
        foreach ($this->elements as $formElement) {
            $isValid = $formElement->isValid();
            if(!$isValid) {

                return false;
            }
        }

        return true;
    }

    /**
     * Ustawia błędy walidacji
     */
    public function setValidatorsErrors() {
        $formElementsValidatorsError = array();
        foreach ($this->elements as $formElement) {
            $elementsValidatorsError = $formElement->getValidatorsErrors();
            if($elementsValidatorsError) {
                $formElementName = $formElement->getName();
                $formElementsValidatorsError[$formElementName] = $elementsValidatorsError;
            }
        }
        $this->validatorsErrors = $formElementsValidatorsError;
    }

    /**
     * Zwraca błedy walidatorów elementów formularza
     *
     * @return array tablica błędów walidatorów elementów formularza
     */
    public function getValidatorsErrors() {

        return $this->validatorsErrors;
    }
}