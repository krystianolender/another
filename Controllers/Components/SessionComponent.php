<?php

/**
 * Komponent dający dostęp do sesji serwera
 *
 * Odciąża od rozpoczynania sesji i zabezpiecza przestrzenie nazw sesji zarezerwowane dla autentykacji i wiadomości
 */
class SessionComponent implements Component{
    public static $session = null;
    public $sessionData = array();
    private $notAllowedNames = array(
        'user',
        'message'
    );

    public static function getInstance() {
        if(!isset(self::$session) || !self::$session) {
            self::$session = new SessionComponent();
        }

        return self::$session;
    }

    private function __construct() {
        $this->sessionStart();
    }

    /**
     * Rozpoczyna sesje
     */
    private function sessionStart() {
        if(!$this->isSessionStarted()) {
            session_start();
        }
    }

    /**
     * Sprawdza czy sesja została już uruchomiona
     *
     * @return boolean true jeżeli sesja została już uruchomiona
     */
    private function isSessionStarted() {
        $sessionId = session_id();
        if(!empty($sessionId)) {
            $isSessionStarted = true;
        } else {
            $isSessionStarted = false;
        }

        return $isSessionStarted;
    }

    /**
     * Dodaje do sesji wartość pod podaną nazwą.
     * Sprawdza czy nie narusza zarezerwowanej przestrzeni nazw
     *
     * @param string $name - nazwa wartości
     * @param type $value
     */
    public function write($name, $value) {
        if($this->isAllowed($name)) {
            $_SESSION[$name] = $value;
        }

        $this->sessionData = $_SESSION;
    }

    /**
     * Zwraca z sesji wartość dla danej nazwy
     *
     * @param string $name - nazwa wartości
     * @return mixed wartość przechowywana w sesji pod daną nazwą
     */
    public function read($name) {
        if($this->check($name) && $this->isAllowed($name)) {
            $dataFromSession = $_SESSION[$name];
        } else {
            $dataFromSession = null;
        }

        return $dataFromSession;
    }

    /**
     * Sprawdza czy dana nazwa w sesji jest dozwolona
     *
     * @param string $name - nazwa wartości
     * @return boolean true jeżeli nazwa w sesji jest dozwolona
     */
    private function isAllowed($name) {
        if(in_array($name, $this->notAllowedNames)) {
            $isAllowed = false;
        } else {
            $isAllowed = true;
        }

        return $isAllowed;
    }

    /**
     * Sprawdza czy w sesji istnieje wpis o podanej nazwie i czy posiada wartość
     *
     * @param string $name - nazwa wartości
     * @return boolean true jeżeli w sesji istnieje wpis o podanej nazwie i nie jest on pusty
     */
    public function check($name) {
        if(isset($_SESSION[$name]) && !empty($_SESSION[$name])) {
            $isInSession = true;
        } else {
            $isInSession = false;
        }

        return $isInSession;
    }

    /**
     * Usuwa z sesji wpis o podanej nazwie
     *
     * @param string $name - nazwa wartości
     */
    public function delete($name) {
        if($this->isAllowed($name)) {
            if($this->check($name)) {
                unregister($name);
            }
        }

        $this->sessionData = $_SESSION;
    }
}