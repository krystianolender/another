<?php

class View {
    private $data = array();
    private $file;
    private $defaultLayout = 'layout';
    private $layout;
    public $auth;

    /**
     * Domyślne helpery widoków
     */
    public $helpers = array(
        'Url',
        'Image'
    );

    public function __construct($file) {
        $this->file = $file;
        $this->setLayout();
        $this->setHelpers();
    }

    /**
     * Ustala i ustawia plik widoku
     *
     * @param string $file - nazwa pliku widoku
     * @param string $folder - nazwa katalogu z plikiem widoku (domyślnie katalog adekwatny do aktualnego modelu/kontrolera)
     */
    public function setFile($file, $folder = null) {
        if(!$folder) {
            $folder = Router::getModel();
        }

        if(!strstr($file, '.php')) {
            $file = $file . '.php';
        }

        $this->file = VIEW . '/' . $folder . '/' . $file;
    }

    /**
     *  Ustawia aktualny layout dla widoku
     *
     * @param string $layout - nazwa layoutu
     */
    public function setLayout($layout = null) {
        if(!$layout) {
            $layout = $this->defaultLayout;
        }

        $this->layout = LAYOUT . '/' . $layout . '.php';
    }

    /**
     * Dodaje do widoku wszyskie helpery wymienione w tablicy $helpers
     */
    public function setHelpers() {
        foreach ($this->helpers as $helper) {
            if(!isset($this->$helper )) {
                $helperName = $helper . 'Helper';
                Loader::loadHelper($helperName);
                $this->$helper = new $helperName();
            }
        }
    }

    /**
     *  Dodaje do widoków komunikaty
     */
    public function setMessagesToView() {
        Loader::loadComponent('Message');
        $messageComponent  = MessageComponent::getInstance();
        $messages = $messageComponent->getMessages();
        $messageComponent->deleteMessages();

        $this->set('messages', $messages);
    }

    /**
     *  Dodawaie helpera do widoku
     *
     * @param string $helper - nazwa helpera widoku
     */
    public function addHelper($helper) {
        $this->helpers[] = $helper;
        $this->setHelpers();
    }

    /**
     *  Wysyła zmienną do widoku
     *
     * @param mixed $variable - zmienna przekazywana do widoku
     * @param type $value - wartość zmiennej przekazywanej do widoku
     */
    public function set($variable, $value) {
        $this->data[$variable] = $value;
    }

    /**
     * Renderuje widok
     */
    public function render() {
        if($this->layout) {
            $this->renderWithLayout();
        }  else {
            $this->renderWithoutLayout();
        }
    }

    /**
     * Dodaje layout do widoku
     */
    private function renderWithLayout() {
        $this->set('viewContent', $this->file);
        extract($this->data);
        include($this->layout);
    }

    /**
     * Renderuje widok bez layoutu
     */
    private function renderWithoutLayout() {
        extract($this->data);
        include($this->file);
    }
}