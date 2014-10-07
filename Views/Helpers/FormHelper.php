<?php

/**
 * Helper widoku generujący kod HTML dla formularzy.
 *
 * Najważniejszą funkcją jest toHTML() - generuje ona kod HTML formularza.
 * Helper pozwala dodawać etykiety, kod walidatorów błędów oraz inny kod HTML do elementów formularza.
 */
class FormHelper {
    private $form = null;
    private $labels = array();
    private $formHtml = null;
    private $formHtmlDomDocument;
    private $elementsSeparator = "\n";

    /**
     * Ustawia obiekt dlasy FormComponent dla helpera formularza
     * Jeżeli formularz nie posiada przycisku typu submit będzi on dodany automatycznie
     *
     * @param FormComponent $form - obiekt formularza, który ma być wyświetlony
     */
    public function setForm(FormComponent $form) {
        $this->form = $form;

        if (!$this->isSubmitElementInForm()) {
            $this->addDefaultSubmitElementToForm();
        }
    }

    /**
     * Sprawdza czy w obiekcie FormComponent znajduje się przycisk typu submit
     *
     * @return boolean true jeżeli w obiekcie FormComponent znajduje się przycisk typu submit
     */
    public function isSubmitElementInForm() {
        $isSubmitElementInForm = false;
        $elements = $this->form->getElements();
        foreach ($elements as $element) {
            if ($element->getType() == FormElementComponent::SUBMIT_TYPE) {
                $isSubmitElementInForm = true;

                break;
            }
        }

        return $isSubmitElementInForm;
    }

    /**
     * Dodaje do obiektu FormComponent przycisk typu Submit
     */
    public function addDefaultSubmitElementToForm() {
        Loader::loadComponent('FormElement');
        $submitElement = new FormElementComponent('Wyślij', FormElementComponent::SUBMIT_TYPE);
        $this->form->AddElement($submitElement);
    }

    /**
     * Zwraca obiekt FormComponent
     *
     * @return obiekt FormComponent
     */
    public function getForm() {

        return $this->form;
    }

    /**
     * Dodaje etykietę do elementu formularza
     *
     * @param string $element - nazwa elementu do którego ma być dodana etykieta
     * @param string $label - treść etykiety
     */
    public function addLabel($element, $label) {
        $this->labels[$element] = $label;
    }

    /**
     * Ustala identyfikator elementu formularza
     * Jeżeli nie zaostanie podana nazwa identyfikatora elementu formularza to będzie ona wygenerowana automatycznie
     *
     * @param string $element - nazwa elementu formularza
     * @param string $id - identyfikator elementu formularza
     */
    public function setElementId($element, $id = null) {
        if (isset($this->form->elements[$element])) {
            if (isset($id) && !empty($id)) {
                $this->setGivenElementId($element, $id);
            } else {
                $this->setDefaultElementId($element);
            }
        }
    }

    /**
     * Ustawia domyślny identyfikator dla elementu formularza
     *
     * @param string $element - nazwa elementu formularza
     */
    private function setDefaultElementId($element) {
        $elementName = $this->form->elements[$element]->name;
        $camelizeElementName = camelize($elementName);
        $formName = $this->form->name;
        $camelizeFormName = camelize($formName);
        $id = $camelizeFormName . $camelizeElementName;

        $this->form->elements[$element]->id = $id;
    }

    /**
     * Ustawia w elemencie formularza wskazaną wartość identyfikatora
     * Identyfikator jest ustawiany dla obiektu FormElementComponent znajdującego się w kolekcji w obiekcie klasy FormComponent
     *
     * @param string $element - nazwa elementu formularza
     * @param string $id - identyfikator elementu formularza
     */
    private function setGivenElementId($element, $id) {
        $this->form->elements[$element]->id = $id;
    }

    /**
     * Ustawia separator pomiędzy elementami formularza
     *
     * @param string $separator - znaczkik którym mają być oddzielone elementy formularza
     */
    public function setSeparatorBetweenElements($separator) {
        $this->elementsSeparator = $separator;
    }

    /**
     * Generuje kod HTML dla formularza.
     * Jeżeli formularz jest typu GET to dodawane są do niego ukryte pola z elementami adresu URL
     */
    public function setHtml() {
        $beginFormHtml = $this->prepareFormBeginHtml();

        if ($this->form->getMethod() == RequestComponent::GET) {
            $this->addPhpGetAddressToElements();
        }

        $elementsFormHtml = $this->prepareFormElementsHtml();
        $endFormHtml = $this->prepareFormEndHtml();

        $this->formHtml = $beginFormHtml . "\n" . $elementsFormHtml . "\n" . $endFormHtml;
    }

    /**
     * Zwraca kod HTML formularza
     * Jeżeli kod nie został wygenerowany zostanie wygenerowany automatycznie
     *
     * @param boolean $withValidators - czy do formularza mają być dodawan komunikaty błędów walidatorów pól formularza
     * @return string kod HTML formularza
     */
    public function getHtml($withValidators = true) {
        if (empty($this->formHtml)) {
            $this->setHtml();
        }

        if($withValidators) {
            $this->addValidatorsErrors();
        }

        return $this->formHtml;
    }

    /**
     * Alias dla funkcji getHtml
     *
     * @return string kod HTML formularza
     */
    public function toHtml() {

        return $this->getHtml();
    }

    /**
     * Tworzy początek kodu HTML formularza
     *
     * @return string $formBeginHtml - początek kodu formularza
     */
    private function prepareFormBeginHtml() {
        $formAction = $this->form->getAddress();
        $htmlTagIdParameter = $this->prepareHtmlTagIdParameter($this->form->getId());
        $formMethod = $this->form->getMethod();
        $formBeginHtml = '<form action="' . $formAction . '" ' . $htmlTagIdParameter . ' method="' . $formMethod . '">';

        return $formBeginHtml;
    }

    /**
     * Dodaje ukryte pola odpowiadające nazwie kontrolera, akcji i parametru w przypadku gdy formularz jest typu GET
     * Zapobiega to nadpisaniu adresu URL przez formularz
     */
    private function addPhpGetAddressToElements() {
        if (Router::getParameter()) {
            $parameterElement = new FormElementComponent('parameter', FormElementComponent::HIDDEN_TYPE, Router::getParameter());
            $this->form->addElement($parameterElement);
        }

        $actionElement = new FormElementComponent('action', FormElementComponent::HIDDEN_TYPE, Router::getAction());
        $this->form->addElement($actionElement);

        $controllerElement = new FormElementComponent('controller', FormElementComponent::HIDDEN_TYPE, Router::getModel());
        $this->form->addElement($controllerElement);
    }

    /**
     * Generuje fragmenty kodu HTML identyfikatora znacznika
     *
     * @param string $id - identyfikator znacznika
     * @return string kod HTML identyfikatora formularza
     */
    private function prepareHtmlTagIdParameter($id = null) {
        $htmlTagIdParameter = $this->prepareHtmlTagParameter('id', $id);

        return $htmlTagIdParameter;
    }

    /**
     * Generuje fragment kodu HTML wartości znacznika
     *
     * @param string $value - wartość znacznika
     * @return string fragment kodu HTML identyfikatora znacznika
     */
    private function prepareHtmlTagValueParameter($value = null) {
        $htmlTagValueParameter = $this->prepareHtmlTagParameter('value', $value);

        return $htmlTagValueParameter;
    }

    /**
     * Generuje fragment kodu HTML w postaci właściwość = wartość
     *
     * @param string $parameter - nazwa parametru znacznika
     * @param string $value - wartość parametru znacznika
     * @return string fragment kodu HTML właściwości znaczniak
     */
    private function prepareHtmlTagParameter($parameter, $value = null) {
        if ($value) {
            $htmlTagParameter = $parameter . '="' . $value . '"';
        } else {
            $htmlTagParameter = null;
        }

        return $htmlTagParameter;
    }

    /**
     * Generuje kod HTML elementu formularza
     *
     * @return string kod HTML elementu formularza
     */
    public function prepareFormElementsHtml() {
        $inputsHtml = array();
        $formElements = $this->form->getElements();

        foreach ($formElements as $formElement) {
            $inputsHtml[] = $this->labelToHtml($formElement);
            $inputsHtml[] = $this->elementToHtml($formElement);
        }
        $inputHtml = arrayToString($inputsHtml, $this->elementsSeparator);
        $inputHtmlWithSeparatorAtEnd = $inputHtml . $this->elementsSeparator;

        return $inputHtmlWithSeparatorAtEnd;
    }

    /**
     * Generuje kod HTML etykiety formularza
     *
     * @param string $formElement - nazwa elementu
     * @return string $labelHtml - kod HTML etykiety formularza
     */
    private function labelToHtml($formElement) {
        $elementName = $formElement->getName();
        if (isset($this->labels[$elementName])) {
            $this->setDefaultLabelIdIfNotExist($formElement);
            $labelFor = $formElement->getName();
            $labelHtml = '<label for="' . $labelFor . '" class="control-label">' . $this->labels[$elementName] . '</label>';
        } else {
            $labelHtml = null;
        }

        return $labelHtml;
    }

    /**
     * Generuje kod HTML elementu formularza
     * @param obiekt FormElementCOmponent $formElement - element formularza dla którego ma być wygenerowany kod HTML
     * @return string $inputHtml - kod HTML elementu formularza
     */
    private function elementToHtml($formElement) {
        $elementId = $formElement->getId();
        $htmlTagIdParameter = $this->prepareHtmlTagIdParameter($elementId);
        $elementName = $formElement->getName();
        $elementType = $formElement->getType();
        $elementValue = $formElement->getValue();
        $htmlTagValueParameter = $this->prepareHtmlTagValueParameter($elementValue);
        $inputHtml = '<input name="' . $elementName . '" type="' . $elementType . '" ' . $htmlTagIdParameter . ' ' . $htmlTagValueParameter . '>';

        return $inputHtml;
    }

    /**
     * Dodaje domyślną wartość identyfikatora do obiektu klasy FormElementComponent jeżeli nie jest ustawiony
     *
     * @param obiekt FormElementComponent $formElement - element formularza do którego ma być dodany identyfikator
     */
    private function setDefaultLabelIdIfNotExist($formElement) {
        $elementId = $formElement->getId();
        if (!isset($elementId) || empty($elementId)) {
            $elementName = $formElement->getName();
            $this->setElementId($elementName);
        }
    }

    /**
     * Generuje kod HTML kończący formularz HTML
     *
     * @return string kod HTML kończączy formularz
     */
    public function prepareFormEndHtml() {
        $formEndHtml = '</form>';

        return $formEndHtml;
    }

    /**
     * Dodaje kod HTML walidatorów błędów dla elementów formularza
     *
     * @param array $errors - tablica błędów walidatora formularza
     */
    public function addValidatorsErrors(array $errors = array()) {
        if(!$errors) {
            $errors = $this->form->getValidatorsErrors();
        }

        $this->setFormHtmlDomDocument();
        $inputs = $this->formHtmlDomDocument->getElementsByTagName('input');
        $this->addInputsErrorClassAndDiv($inputs, $errors);

        $this->generateFormHtmlFromDomDocument();
    }

    /**
     * Tworzy obiekt DOMDocument dla formularza
     *
     * @return obiekt DOMDocument
     */
    private function setFormHtmlDomDocument() {
        $this->formHtmlDomDocument = new DOMDocument();
        @$this->formHtmlDomDocument->loadHTML($this->formHtml);
        $this->formHtmlDomDocument->preserveWhiteSpace = false;

        return $this->formHtmlDomDocument;
    }

    /**
     * Dodaje do elementu formularza właściwość klasy css i następujący do div z treścią błędu walidatora
     *
     * @param NodeList $inputs - pola formularza
     * @param array $errors - tablica błędów walidatorów formularza
     */
    public function addInputsErrorClassAndDiv($inputs, $errors) {
        foreach ($inputs as $input) {
            if($this->inputHasErrors($input, $errors)) {
                $this->addErrorCssClassToInput($input);
                $this->addErrorDivAfterInput($input, $errors);
            }
        }
    }

    /**
     * Sprawdza czy pole formularza ma błędy walidatorów
     *
     * @param DOMNode $input - pole formularza
     * @param array $errors - tablica błędów walidatorów
     * @return boolean true jeżeli pole formularza ma błędy walidatorów
     */
    private function inputHasErrors($input, $errors) {
        $inputName = $input->getAttribute('name');
        if(array_key_exists($inputName, $errors)) {
            $inputHasErrors = true;
        } else {
            $inputHasErrors = false;
        }

        return $inputHasErrors;
    }

    /**
     * Dodaje atrybut CLASS CSS klasy błędu do pola formularza
     *
     * @param DOMNode $input - pole formularza
     */
    public function addErrorCssClassToInput($input) {
        $input->setAttribute('class', 'error');
    }

    /**
     * Dodaje DIV HTML z komuniktem błędu formularza po polu formularza
     *
     * @param DOMNode $input - pole formularza
     * @param array $errors - komunikty błędów formularza
     */
    function addErrorDivAfterInput($input, $errors) {
        $inputName = $input->getAttribute('name');
        foreach ($errors[$inputName] as $inputError) {
            $validatorErrorDiv = $this->formHtmlDomDocument->createElement('div', $inputError);
            $validatorErrorDiv->setAttribute('class', 'error');
            $input->parentNode->insertBefore($validatorErrorDiv, getNodeListNextElement($input));
        }
    }

    /**
     * Generuje kod HTML formularza z obiektu DOMDocument
     */
    private function generateFormHtmlFromDomDocument() {
        $this->formHtmlDomDocument->formatOutput = true;
        $html = $this->formHtmlDomDocument->saveHTML();

        $this->formHtml = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $html));
    }
}