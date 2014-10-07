<?php
/**
 * Klasa autoruzacji.
 *
 * Pozwala sprawdzić czy zalogowana osoba ma dostęp do aktualnego adresu.
 * Najważniejszą akcją jest authorize() - wynik jej działania okreśła dostęp lub jego brak dla użytkownika do adresu.
 */
class AuthorizationComponent implements Component {
    private $controller;
    private $action;
    private $parameter;
    private $model;
    private $auth;

    public static function getInstance() {

        return new AuthComponent();
    }

    public function __construct() {
        Loader::loadComponent('Auth');
        $this->auth = AuthComponent::getInstance();

        $this->controller = Router::getController();
        $this->action = Router::getAction();
        $this->parameter = Router::getParameter();
        $this->model = Router::getModel();
    }

    /**
     * Sprawdza czy użytkownik może uzyskać dostęp do akcji
     * Administrator ma zawsze dostęp
     *
     * @return boolean true jeżli użytkownik ma dostęp do akcji
     */
    public function authorize() {
        $userCanGoToAction = false;
        if($this->auth->isLoggedIn()) {
            if($this->auth->isAdmin()) {
                $userCanGoToAction = true;
            } else {
                $userCanGoToAction = $this->loggedUserCanGoToAction($this->controller, $this->action, $this->parameter = null);
            }
        } else {
            $userCanGoToAction = false;
        }

        return $userCanGoToAction;
    }

    /**
     * Sprawdza czy zalogowany użytkownik ma dostęp do akcji
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @param mixed $parameter - parametry akcji
     * @return boolean true jeżeli zalogowany użytkownik ma dostęp do akcji
     */
    private function loggedUserCanGoToAction($controller, $action, $parameter) {

        return $this->userCanGoToAction($this->auth->readLoggedUser(), $controller, $action, $parameter);
    }

    /**
     * Sprawdza czy podany użytkownik ma dostęp do akcji
     *
     * @param User $user - użytkownik dla którego ma być sprawdzone prawo dostępu do akcji
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @param mixed $parameter - parametry akcji
     * @return boolean true jeżeli podany użytkownik ma dostęp do akcji
     */
    private function userCanGoToAction(User $user, $controller, $action, $parameter = null) {
        $userCanGoToAction = false;
        $actionAuthorization = $this->getActionAuthorization($controller, $action);
        if(!$actionAuthorization) {
            $userCanGoToAction = false;
        } else {
            if($actionAuthorization == 'user') {
                $userCanGoToAction = true;
            } elseif($actionAuthorization == 'owner' && $parameter) {
                $userCanGoToAction = $this->isUserObjectOwner($this->model, $parameter, $user['id']);
            }
        }

        return $userCanGoToAction;
    }

    //TODO::dokończyć
    /**
     *
     */
    public function unAuthorize() {

    }

    /**
     * Pobiera autoryzacje Akcji
     *
     * TODO::to się nie zgadza!
     *
     * Jeżeli akcja jest w tablicy allowedActions akcja jest dostępna dla każdego
     *
     * @param string $controller - nazwa kontrolera
     * @param string $action - nazwa akcji
     * @return true jeżeli akcja jest dozwolona
     */
    private function getActionAuthorization($controller, $action) {
        $controllerAuthorization = $this->getControllerAuthorization($controller);
        if(isset($controllerAuthorization[$action]) && $controllerAuthorization[$action]) {
            $actionAuthorization = $controllerAuthorization[$action];
        } else {
            $actionAuthorization = false;
        }

        return $actionAuthorization;
    }

    /**
     *  Pobiera autoryzacje akcji kontrolera
     *
     * @param string $controller - nazwo kontrolera
     * @return array $controllerAuthorization - autoryzacja kontrolera
     */
    private function getControllerAuthorization($controller) {
        $controllerAuthorization = $controller::getAllowedActions();

        return $controllerAuthorization;
    }

    //TODO: czy to nie powinno być w modelu?
    /**
     * Sprawdza czy użytkownik jest właścicielem obiektu do którego stara się uzyskać dostęp
     *
     * @param string $model - nazwa modelu
     * @param integer $id - identyfikator dla modelu
     * @param integer $userId - identyfikator użytkownika
     * @return boolean true jeżeli użytkownik jest właścicielem obiektu
     */
    public function isUserObjectOwner($model, $id, $userId) {
        Loader::loadModel($model);
        $modelObject = new $model();
//        if($modelObject->tableHasOwnerIdColumn) {
//            $isUserObjectOwner =
//        } else {
//            $isUserObjectOwner = false;
//        }
//        $modelColumns = $model->getTableColumns();
//        if(in_array('owner_id', $modelColumns)) {
//            Loader::loadModel($model);
//            $modelObject = new $model();
//            $test = $modelObject->find($id);
//            if(!$test->id) {
//                $isUserObjectOwner = false;
//            } elseif($test->owner_id == $userId) {
//                $isUserObjectOwner = true;
//            }
//        } else {
//            $isUserObjectOwner = false;
//        }

        return $isUserObjectOwner;
    }
}