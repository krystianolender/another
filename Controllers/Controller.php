<?php

/**
 * Główna klasa kontrolerów
 *
 * Definiuje kontroler wzorca MVC i przechowuje wspólne dla innych kontrolerów zachowania i właściwości związane z posiadanymi modelami i widokami oraz autoryzacją.
 * Zapewnia dostęp do domyślnych komponentów w kontrolerach.
 */
class Controller {
    public $view;
    protected $model;
    protected static $controllerAuthorize = array();
    protected static $allowedAction = array();

    protected $allowed = array();
    protected $authorize = array();

    public $components = array(
        'form',
        'request',
        'auth',
        'session',
        'message'
    );

    public function __construct() {
        $this->setModel();
        $this->setView();

        $this->loadComponents();
        self::$allowedAction = $this->allowed;
        self::$controllerAuthorize = $this->authorize;

        $loggedUser = $this->auth->loggedUser();
        $this->view->set('loggedUser', $loggedUser);
    }

    /**
     * Ustawia domyślny model dla kontrolera
     * Tworzy jego obiekt jako właściwość kontrolera pod model
     */
    private function setModel() {
        $modelName = Router::getModel();
        Loader::loadModel($modelName);
        $this->model = new $modelName;
    }

    /**
     * Ustawia domyślny widok dla kontrolera
     * Tworzy jego obiekt jako właściwość kontrolera pod nazwą view
     */
    public function setView() {
        $viewName = Router::getView();
        $modelName = Router::getModel();
        $viewFile = DIRECTORY . '/Views/' . $modelName . '/' . $viewName . '.php';

        Loader::loadView();
        $this->view = new View($viewFile);
    }

    /**
     * Tworzy jako właściwości obiekty komponentów których nazwy znajdują się w tablicy $this->components kontrolera
     */
    public function loadComponents() {
        if(isset($this->components) && !empty($this->components)) {
            foreach ($this->components as $component) {
                Loader::loadComponent($component);
                $componentClassName = $component.'Component';
                $this->$component = $componentClassName::getInstance();
            }
        }
    }

    /**
     * Zwraca dozwolone akcje dla kontrolera
     *
     * @return type
     */
    public static function getAllowedActions() {

        return self::$allowedAction;
    }

    /**
     * Zwraca autoryzacje kontrolera
     *
     * @return type
     */
    public static function getControllerAutorize() {

        return self::$controllerAuthorize;
    }
}