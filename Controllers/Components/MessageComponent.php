<?php

Loader::loadComponent();

/**
 * Komponent zarządzający wiadomościami w systemie.
 *
 * Wiadomości są małymi komunikatami wyświetlanymi użytkownikom w systemie po wykonaniu przez nich czynności (np. komunikat błędu).
 */
class MessageComponent implements Component {
    private $messageSession = null;

    public function __construct() {
        Loader::loadComponent('MessageSession');
        $this->messageSession = MessageSessionComponent::getInstance();
    }

    public static function getInstance() {

        return new MessageComponent();
    }

    /**
     * Dodaje kolejną wiadomość do listy wiadomości
     *
     * @param string $name - nazwa wiadomości
     * @param string $message - wiadomość do wyświetlenia
     */
    public function addMessage($name, $message) {
        $this->messageSession->writeMessage($name, $message);
    }

    /**
     * Pobiera wiadomość o podanej nazwie
     *
     * @param string $name
     * @return string treść wiadomości
     */
    public function getMessage($name) {

        return $this->messageSession->readMessage($name);
    }

    /**
     * Pobiera wszystkie wiadomości
     *
     * @return array treść wiadomości
     */
    public function getMessages() {

        return $this->messageSession->readMessages();
    }

    /**
     * Usuwa z listy wiadomości wiadomość o podanej nazwie
     *
     * @param string $name - nazwa wiadomości
     */
    public function deleteMessage($name) {
        $this->messageSession->delete($name);
    }

    /**
     * Czyści listę wiadomości
     *
     */
    public function deleteMessages() {
        $this->messages = null;
        $this->messageSession->deleteAll();
    }
}