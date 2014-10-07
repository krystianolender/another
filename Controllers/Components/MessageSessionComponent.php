<?php

Loader::loadComponent('Session');

/**
 * Komponent zarządzania sesją wiadomości.
 *
 * Wiadomości są przechowywane w zarezerwowanej przestrzeni nazw sesji.
 */
class MessageSessionComponent {
    private $session;
    private $SessionClassName;

    public static function getInstance() {
        $messageSession = new MessageSessionComponent();

        return $messageSession;
    }

    public function __construct() {
        $this->session = SessionComponent::getInstance();
        $this->SessionClassName = get_class($this->session);
    }

    /**
     * Dodaje wiadomość do listy z sesji
     *
     * @param string $name - nazwa wiadomości
     * @param string $message - wiadomość
     */
    public function writeMessage($name, $message) {
        $_SESSION['message'][$name] = $message;

        $this->sessionData = $_SESSION;
    }

    /**
     * Czytanie wiadomości o podanej nazwie
     *
     * @param string $name - nazwa wiadomości
     * @return string treść wiadomości
     */
    public function readMessage($name) {
        if($this->checkMessageSession($name)) {

            return $_SESSION['message'][$name];
        }
    }

    /**
     * Czyta wszystkie wiadomości
     *
     * @return array tablica wiadomości
     */
    public function readMessages() {
        if($this->check('message')) {

            return $_SESSION['message'];
        }
    }

    /**
     * Usuwa wiadomość o podanej nazwie
     *
     * @param string $name - nazwa wiadomości
     */
    public function delete($name) {
        if($this->checkMessageSession($name)) {
            unregister($_SESSION['message'][$name]);
        }

        $this->sessionData = $_SESSION;
    }

    /**
     * Usuwa wszystkie wiadomości
     */
    public function deleteAll() {
        if($this->check('message')) {
            unset($_SESSION['message']);
        }

        $this->sessionData = $_SESSION;
    }

    /**
     * Sprawdza czy istnieje wiadomość o podanej nazwia i czy ma ona treść
     *
     * @param string $name - nazwa wiadomości
     * @return boolean true jeżeli wiadomość o podanej nazwie istnieje i nie jest pusta
     */
    public function checkMessageSession($name) {
        if(isset($_SESSION['message'][$name]) && !empty($_SESSION['message'][$name])) {
            $isInSession = true;
        } else {
            $isInSession = false;
        }

        return $isInSession;
    }

    /**
     * Metoda magiczna wywołująca na obiekcie Session metody nie istniejącej w klasie MessageSession
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
     * Metoda magiczna pobierająca z obiektu klasy Session właściwość nie istniejącą w obiekcie klasy MessageSession
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
}