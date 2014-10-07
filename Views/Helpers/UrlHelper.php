<?php

/**
 * Helper widoku generujący kod HTML dla znacznika A.
 *
 * Najważniejszą funkcją jest toHTML() - generuje ona kod HTML znacznika A.
 * Helper uzupełnia adres w odnośniku o wartości domyślne jego elementów
 */
class UrlHelper{

    /**
     * Generuje kod HTML dla odnośnika
     *
     * @param string $text - treść odnośnika
     * @param string $controller - nazwa kontrolera do którego ma prowadzić odnośnik
     * @param string $action - nazwa akcji do której ma prowadzić odnośnik
     * @param string $parameter - wartość parametru odnośnika
     * @return string $link - kod HTML odnośnika
     */
    public function toHtml($text, $controller = null, $action = null, $parameter = null) {
        $url = $this->prepareUrl($controller, $action, $parameter);
        $link = '<a href="' . INDEX . $url . '">' . $text . '</a>';

        return $link;
    }

    /**
     * Generuje adres URL.
     * Adres ma formę ?controller=kontroler&action=... tak by mógł być dodany do adresu w stylu www.domena.com/index.php
     * Jeżeli nie zostaną podane wartości dla elementu adresu, będą one zastąpione wartościami domyślnymi
     *
     * @param string $controller - nazwa kontrolera do którego ma prowadzić odnośnik
     * @param string $action - nazwa akcji do której ma prowadzić odnośnik
     * @param string $parameter - wartość parametru odnośnika
     * @return string treść fragmentu adresu URL
     */
    private function prepareUrl($controller = null, $action = null, $parameter = null) {
        $urlElements = array();
        $urlElementsWithController = $this->addController($urlElements, $controller);
        $urlElementsWithControllerAndAction = $this->addAction($urlElementsWithController, $action);
        $urlElementsWithControllerAndActionAndParameter = $this->addParameter($urlElementsWithControllerAndAction, $parameter);
        $url = implode('&', $urlElementsWithControllerAndActionAndParameter);

        return '?' . $url;
    }

    /**
     * Dodaje kontroler do adresu URL.
     * Jeżeli wartość kontrolera nie będzie podana zostanie ona zastąpiona wartością domyślną.
     *
     * @param array $urlElements - fragmenty odnośnika
     * @param string $controller - nazwa kontrolera do którego ma prowadzić odnośnik
     * @return array $urlElements - fragmenty odnośnika z kontrolerem
     */
    private function addController(array $urlElements, $controller = null) {
        $controller = ucfirst($controller);
        if($controller) {
            $urlElements[] = 'controller=' . $controller;
        } else {
            $urlElements[] = 'controller=' . Router::$defaultController;
        }

        return $urlElements;
    }

    /**
     * Dodaje akcje do adresu URL.
     * Jeżeli wartość akcji nie będzie podana zostanie ona zastąpiona wartością domyślną.
     *
     * @param array $urlElements - fragmenty odnośnika
     * @param type $action
     * @return array $urlElements - fragmenty odnośnika z akcją
     */
    private function addAction($urlElements, $action = null) {
        $action = lcfirst($action);
        if($action) {
            $urlElements[] = 'action=' . $action . 'Action';
        } else {
            $urlElements[] = 'action=' . Router::$defaultAction . 'Action';
        }

        return $urlElements;
    }

    /**
     * Dodaje parametr do adresu URL
     *
     * @param array $urlElements - fragmenty odnośnika
     * @param string $parameter - parametr
     * @return array $urlElements - fragmenty odnośnika z akcją
     */
    private function addParameter($urlElements, $parameter) {
        if($parameter) {
            $urlElements[] = 'parameter=' . $parameter;
        }

        return $urlElements;
    }
}