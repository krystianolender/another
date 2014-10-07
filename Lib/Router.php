<?php

/**
 * Klasa zarządzająca adresami URL, przekierowująca do odpowiedniego kontrolera i akcji oraz wykonująca niezbędne czynności przed przekierowaniem do miejsca w aplikacji
 */
class Router {
    const CONTROLLER_WORD = 'Controller';

    public static $defaultController = 'User';
    public static $defaultAction = 'indexAction';
    public static $actionWord = 'action';
    public static $controllerWord = 'controller';
    public static $parameterWord = 'parameter';

    private static $controller;
    private static $action;
    private static $parameter;
    private static $model;
    private static $view;

    private $message;

    /**
     * Pobiera z adresu nazwę kantrolera
     * Jeżeli w adresie nie jest podana nazwa kontrolera pobierana jest domyślna wartość kontrolera
     *
     * @return string nazwa kontrolera
     */
    public static function getController() {
        if(!self::$controller) {
            if(isset($_GET[self::$controllerWord])) {
                $controller = $_GET[self::$controllerWord] . 'Controller';
                self::$controller = ucfirst($controller);
            } else {
                self::defaultRedirect();
            }
        }

        return self::$controller;
    }

    /**
     * Generuje nazwę kontrolera na potrzeby adresu URL (bez słowa Controller)
     *
     * @return string $controllerForUrl - nazwa kontrolera dla adresu url
     */
    public function getControllerForUrl() {
        $controllerForUrl = self::getModel();

        return $controllerForUrl;
    }

    /**
     * Pobiera z adresu nazwę akcji
     * Jeżeli w adresie nie jast podana nazwa akcji pobierana jest domyślna wartość akcji
     *
     * @return type
     */
    public static function getAction() {
        if(!isset($_GET[self::$actionWord]) || empty($_GET[self::$actionWord])) {

            //TODO: usunąć słowo Action
            self::$action = self::$defaultAction;
        } else {
            self::$action = $_GET[self::$actionWord];
        }

        return self::$action;
    }

    /**
     *  Pobiera z adresu wartość parametru
     *
     * @return mixed wartość parametru przekazywanego przez adres
     */
    public static function getParameter() {
        if(!self::$parameter && !isset($_GET[self::$parameterWord]) && !empty($_GET[self::$parameterWord])) {
            self::$parameter = $_GET[self::$parameterWord];
        }

        return self::$parameter;
    }

    /**
     * Generuje nazwę domyślnego modelu dla aktualnego kontrolera
     *
     * @return string nazwa modelu
     */
    public static function getModel() {
        $controller = self::getController();
        $currentControllerLenth = strlen($controller);
        $controllerWordLenth = strlen(Router::CONTROLLER_WORD);
        $controllerName = substr($controller, 0, $currentControllerLenth - $controllerWordLenth);

        self::$model = $controllerName;

        return self::$model;
    }

    /**
     * Generuje nazwę widoku dla aktualnej akcji kontrolera
     *
     * @return type
     */
    public static function getView() {
        if(!self::$view) {
            $action = self::getAction();
            $uncamelizeViewName = uncamelize($action);
            $uncamelizeViewNameWithoutAction = self::removeActionWordFromView($uncamelizeViewName);
            self::$view = $uncamelizeViewNameWithoutAction;
        }

        return self::$view;
    }

    /**
     * Usuwa słowo Action z nazwy akcji
     *
     * @param string $actionName - nazwa akcji ze słowem Action
     * @return string $actionNameWithoutActionWord - nazwa akcji bez słowa Action
     */
    private static function removeActionWordFromView($actionName) {
        $actionNameElements = explode('_', $actionName);
        $numberOfActionNameElement = count($actionNameElements);
        if($actionNameElements[$numberOfActionNameElement - 1] == self::$actionWord) {
            unset($actionNameElements[$numberOfActionNameElement - 1]);
            $actionNameWithoutActionWord = implode('_', $actionNameElements);
        } else {
            $actionNameWithoutActionWord = $actionName;
        }

        return $actionNameWithoutActionWord;
    }

    /**
     * Generuje adres url w notacji index.php?controler=kontroler&action=akcja[&parameter=wartość]
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @param mixed $parameter - wartość parametra
     * @return string adres url
     */
    public static function generateUrl($controller = null, $action = null, $parameter = null) {
        $urlElements = array();
        $urlElements[] = self::generateUrlController($controller);
        $urlElements[] = self::generateUrlAction($action);
        $urlElements[] = self::generateUrlParameter($parameter);
        $controllerActionParameter = arrayToString($urlElements, '&');
        $url = INDEX . '?' . $controllerActionParameter;

        return $url;
    }

    /**
     * Generuje fragment adresu url odpowiedzialny za kontroler
     *
     * @param string $controller - nazwa kontrolera
     * @return string fragment adresu url odpowiedzialny za kontroler
     */
    private static function generateUrlController($controller = null) {
        $controllerUrl = self::generateUrlElement(self::$controllerWord, $controller, Router::$defaultController);

        return $controllerUrl;
    }

    /**
     * Generuje fragment adresu url odpowiedzialnego za akcje
     *
     * @param type $action
     * @return type
     */
    private static function generateUrlAction($action = null) {
        $actionUrl = self::generateUrlElement(self::$actionWord, $action, Router::$defaultAction);

        return $actionUrl;
    }

    /**
     * Generuje fragment adresu ulr odpowiedzialnego za parametr
     *
     * @param mixed $parameter - wartość parametru
     * @return $parameterUrl fragment adresu ulr odpowiedzialnego za parametr
     */
    private static function generateUrlParameter($parameter = null) {
        $parameterUrl = self::generateUrlElement(self::$parameterWord, $parameter);

        return $parameterUrl;
    }

    /**
     * Generuje fragment adresu url podanego typu
     *
     * @param string $urlElementName - typ fragmentu adresu url (self::$actionWord/self::$controllerWord/self::$parameterWord)
     * @param mixed $urlElementValue  - wartość fragmentu adresu url
     * @param mixed $defaultValue - domyślna wartość fragmentu adresu url
     * @return string $urlElement - fragment adresu url
     */
    private static function generateUrlElement($urlElementName, $urlElementValue = null, $defaultValue = null) {
        if($urlElementValue) {
            $urlElement = $urlElementName.'='.$urlElementValue;
        } elseif($defaultValue) {
            $urlElement = $urlElementName.'='.$defaultValue;
        } else {
            $urlElement = $defaultValue;
        }

        return $urlElement;
    }

    /**
     * Przekierowanie do domuślego adresu
     * Zmieniany jest adres url
     * Następuje przekierowanie do domyślego kontrolera, a dalej jest ustalana domyślna akcja
     * Jeżeli użytkownik nie jest zalogowany kierowany jest do widoku logowania
     */
    public static function defaultRedirect() {
        Loader::loadComponent('Auth');
        $auth = AuthComponent::getInstance();
        if($auth->isLoggedIn()) {
            self::redirect(self::$defaultController);
        } else {
            self::redirect('User', 'loginAction');
        }
    }

    /**
     * Przekierowanie do akcji w tym samym kontrolerze
     * Zmieniany jest adres url
     *
     * @param type $action
     */
    public static function redirectToAction($action) {
        self::redirect(self::getModel(), $action);
    }


    //TODO: dodać sprawdzanie czy osoba zalogowana lub akcja nie wymaga logowania
    /**
     * Przekierowanie do podanego kontrolera/akcji/parametru
     * Zmieniany jest adres url
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @param mixed $parameter - wartość parametru
     */
    public static function redirect($controller = null, $action = null, $parameter = null) {
        $url = self::generateUrl($controller, $action, $parameter);
        header('Location: ' . $url);
        die();
    }

    /**
     * Uruchomienie akcji danego kontrolera
     * Sprawdzany jest istnienie akcji i dostęp do niej
     *
     * @param string $action - nazwa akcji
     * @param string $controller - nazwa kontrolera
     */
    public static function toAction($action, $controller) {
        Loader::loadComponent('Message');
        $this->message = MessageComponent::getInstance();

        $isActionExist = $this->isActionExistIfNotSetMessage($action, $controller);
        $isUserLoggedOrActionAllowed = $this->isUserLoggedOrActionAllowed();
        if($isActionExist && $isUserLoggedOrActionAllowed) {

            $this->callActionWithParameter($action, $controller);
        }
    }

    /**
     * Sprawdzanie czy akcja o podanej nazwie istnieje. Jeżeli nie wyświetlany jest komunikat.
     *
     * @param string $action - nazwa akcji
     * @param string $controller - nazwa kontrolera     *
     * @return boolean true jeżeli akcja istnieje
     */
    private function isActionExistIfNotSetMessage($action, $controller) {
        $isActionExist = self::isActionExist($action, $controller);
        if(!$isActionExist) {
            $this->message->addMessage('actionNotExist', 'Akcja nie istnieje');
        }

        return $isActionExist;
    }

    /**
     * Sprawdzanie czy akcja o podanej nazwie istnieje.
     *
     * @param string $action - nazwa akcji
     * @param string $controller - nazwa kontrolera
     * @return boolean true jeżeli akcja istnieje
     */
    public function isActionExist($action, $controller) {
        $controllerMethods = get_class_methods($controller);
        $controllerActions = self::getControllerActions($controllerMethods);
        if(in_array($action, $controllerActions)) {
            $isActionExist = true;
        } else {
            $isActionExist = false;
        }

        return $isActionExist;
    }

    /**
     * Sprawdza czy użytkownik jest zalogowany lub akcja jest dostępna dla niezalogowanych użytkowników.
     *
     * @return boolean true jeżeli użytkownik jest zalogowany lub akcja jest dostępna dla niezalogowanych użytkowników.
     */
    private function isUserLoggedOrActionAllowed() {
        Loader::loadComponent('Auth');
        $auth = AuthComponent::getInstance();
        if($auth->isLoggedIn() || $auth->isActionAllowed(Router::$controller, Router::$action)) {
            $isUserLoggedOrActionAllowed = true;
        } else {
            $this->message->addMessage('actionForLogged', 'Strona dostępna dla zalogowanych');
            $loginAddress = AuthComponent::$loginAddress;

            $this->redirect($loginAddress[self::$controllerWord], $loginAddress[self::$actionWord]);
        }

        return $isUserLoggedOrActionAllowed;
    }

    /**
     * Filtruje nazwy metod kontrolera zostawiając tylko akcje
     *
     * @param array $controllerMethods - nazwy metod kontrolera
     * @return array $controllerActions - akcje kontrolera
     */
    private function getControllerActions($controllerMethods) {
        $controllerActions = array();
        foreach ($controllerMethods as $method) {
            if(strstr($method, 'Action') && !strstr($method, 'Actions')) {
                $controllerActions[] = $method;
            }
        }

        return $controllerActions;
    }

    /**
     * Uruchamia akcje danego kontrolera
     * Jeżeli jest podany to używa parametru metody
     *
     * @param string $action - nazwa akcji
     * @param string $controller - nazwa kontrolera
     */
    public function callActionWithParameter($action, $controller) {
        $parameter = Router::getParameter();
        if($parameter) {
            $controller->$action($parameter);
        } else {
            $controller->$action();
        }
    }
}