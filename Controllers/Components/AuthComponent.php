<?php

/**
 * Klasa uwierzytelniania.
 *
 * Potwierdza tożsamość zalogowanej osoby i przechowuje jego dane.
 * Pozwala sprawdzić czy osoba jest zalogowana i czy zalogowana osoba jest administratorem.
 */
Loader::loadComponent();
class AuthComponent implements Component{

    /**
     * Obiekt klasy AuthSessionComponent do obsługi sesji autentykacji
     * @var AuthSessionComponent $authSession
     */
    private $authSession = null;

    /**
     * URL do którego użytkownik jest przekierowywany po zalowaniu
     * @var string $loginRedirect - adres URL
     */
    private $loginRedirect;

    /**
     * URL do którego użytkownik jest przekierowywany po wylogowaniu
     * @var string $loginRedirect - adres URL
     */
    private $logoutRedirect;


    private $user;
    public $loginAddress = array(
        'controller' => 'User',
        'action' => 'login'
    );

    public function __construct() {
        Loader::loadComponent('AuthSession');
        $this->authSession = AuthSessionComponent::getInstance();
    }

    public static function getInstance() {

        return new AuthComponent();
    }

    /**
     * Logowanie użytkownika poprzez dodawanie jego obiektu do sesji
     *
     * @param User $user - użytkownik do zalogowania
     */
    public function login($user) {
        $this->authSession = AuthSessionComponent::getInstance();
        $userColumns = $user->getTableColumns();
        foreach($userColumns as $column) {
            $userData[$column] = $user->$column;
        }
        $this->authSession->writeLoggedUser($userData);

        $this->afterLogin();
    }

    /**
     * Pobiera zalogowanego użytkownika lub jego właściwość
     *
     * @param string $property - wartość właściwości obiektu User zalogowanego użytkownika
     */
    public function loggedUser($property = null) {
        if ($property) {

            return $this->readloggedUserProperty($property);
        } else {

            return $this->readLoggedUserObject();
        }
    }

    /**
     * Pobiera z sesji za pomocą obiektu klasy AuthSession wartość właściwości obiektu klasy User zalogowanego użytkownika
     *
     * @param @param string $property - wartość właściwości obiektu User zalogowanego użytkownika
     * @return mixed wartość właściwości obiektu User zalogowanego użytkownika
     */
    private function readloggedUserProperty($property) {

        return $this->authSession->readLoggedUserProperty($property);
    }

    /**
     * Pobiera obiekt User zalogowanego użytkownika
     * @return User - obiekt zalogowanego użytkownika
     */
    private function readLoggedUserObject() {

        return $this->authSession->readLoggedUser();
    }

    /**
     * Czynności wykonywane po zalogowaniu użytkownika
     */
    private function afterLogin() {
        Router::redirect('User', 'viewAction');
    }

    /**
     * Wylogowywanie użytkownika i wykonuje funkcje afterLogout
     *
     */
    public function logout(){
        $this->authSession->deleteLoggedUser();
        $this->afterLogout();
    }

    /**
     * Czynności wykonywane po wylogowaniu
     */
    private function afterLogout() {
        Router::redirect('user', 'loginAction');
    }

    /**
     * Sprawdza czy użytkownik jest zalogowany
     *
     * @return true jeżeli użytkownik jest zalogowany
     */
    public function isLoggedIn() {
        if($this->authSession->readLoggedUser()) {
            $isLoggedIn = true;
        } else {
            $isLoggedIn = false;
        }

        return $isLoggedIn;
    }

    /**
     * Sprawdza czy zalogowany użytkownik jest administratorem
     *
     * @return boolean true jeżeli zalogowany użytkownik jest administratorem
     */
    public function isAdmin() {
        if(!$this->isLoggedIn()) {
            $isAdmin = false;
        } else {
            $isAdmin = $this->authSession->readLoggedUserProperty('isAdmin');
        }

        return $isAdmin;
    }

    //TODO::dokończyć
    /**
     * Ustala adres do przekierowania po zalogowaniu
     * @param string $loginRedirect adres po zalogowaniu
     */
    public function setLoginRedirect($loginRedirect) {
        $this->loginRedirect = $loginRedirect;
    }

    /**
     * Ustala adres do przekierowania po wylogowaniu
     * @param string $loginRedirect adres po wylogowaniu
     */
    public function setLogoutRedirect($logoutRedirect) {
        $this->logoutRedirect = $logoutRedirect;
    }

    /**
     * Sprawdza czy dostęp do akcji jest dozwolony dla niezalogowanych użytkowników
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @return boolean true jeżeli dostęp do akcji jest dozwolony dla niezalogowanych użytkowników
     */
    private function isActionAllowed($controller, $action) {
        $allowedActions = $this->getControllerAllowedAction($controller);
        if(in_array($action, $allowedActions)) {
            $isActionAllowed = true;
        } else {
            $isActionAllowed = false;
        }

        return $isActionAllowed;
    }

    /**
     * Pobiera akcje dostępne dla niezalogowanych użytkowników
     * @param string $controller  - nazwa kontrolera
     * @return array $controllerAllowedActions - tablica akcji dostępnych dla niezalogowanych użytkowników
     */
    public function getControllerAllowedAction($controller) {
        $controllerAllowedActions = $controller::getAllowedActions();

        return $controllerAllowedActions;
    }
}