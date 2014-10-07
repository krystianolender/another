<?php

Loader::loadComponent('Session');

/**
 * Komponent zarządzania sesją autoryzacji.
 *
 * Dane autoryzacji są przechowywane w zarezerwowanej przestrzeni nazw sesji.
 */
class AuthSessionComponent implements Component {
    private $session;
    private $SessionClassName;

    public static function getInstance() {
        $authSession = new AuthSessionComponent();

        return $authSession;
    }

    public function __construct() {
        $this->session = SessionComponent::getInstance();
        $this->SessionClassName = get_class($this->session);
    }

    /**
     * Dodaje obiekt User do sesji pod kluczem 'user'
     *
     * @param User $user - zalogowany użytkownik
     */
    public function writeLoggedUser($user) {
        $_SESSION['user'] = $user;

        $this->sessionData = $_SESSION;
    }

    /**
     * Pobiera z sesji zalogowanego użytkownika
     *
     * @return User - zalogowany użytkownik
     */
    public function readLoggedUser() {
        if($this->check('user')) {

            return $_SESSION['user'];
        }
    }

    /**
     * Pobiera właściwość klasu User zalogowanego użytkownika
     *
     * @param string $property - właściwość klasy User
     * @return mixed - wartość właściwości klasy User zalogowanego użytkownika
     */
    public function readLoggedUserProperty($property) {
        if($this->check('user')) {

            return $_SESSION['user'][$property];
        }
    }

    /**
     * Metoda magiczna wywołująca na obiekcie Session metody nie istniejącej w klasie AuthSession
     *
     * @param string $method - nazwa poszukiwanej metody
     * @param mixed $arguments - wartości przekazywane dla poszukiwanej metody
     * @return wywołuje podaną metodę dlasy Session lub false jeżeli klasa Session nie posiada podanej metody
     */
    public function __call($method, $arguments) {
        if(classHasMethod($method, $this->SessionClassName)) {

            return call_user_func_array(array($this->session, $method), $arguments);
        } else {

            return false;
        }
    }

    /**
     * Metoda magiczna pobierająca z obiektu klasy Session właściwość nie istniejącą w obiekcie klasy AuthSession
     *
     * @param string $property
     * @return mixed wartość właściwości obiektu klasy Session lub false jeżeli klasa Session nie posiada danej właściwości
     */
    public function __get($property) {
        if(classHasProperty($property, $this->SessionClassName)) {

            return $this->session->$property;
        } else {

            return false;
        }
    }

    /**
     * Usuwa z sesji dane zalogowanego użytkownika
     */
    public function deleteLoggedUser() {
        unset($_SESSION['user']);
    }
}