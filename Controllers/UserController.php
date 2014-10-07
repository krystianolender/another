<?php

/**
 * Kontroler zarządzający akcjami użytkowników w systemie
 */
class UserController extends Controller {
    private $loginForm;
    private $editForm;
    private $registerForm;
    protected $allowed = array(
        'login'
    );

    protected $authorize = array(
        'edit' => 'owner',
        'view' => 'owner'
    );

    public function loginAction() {
        $this->setLoginForm();
        if($this->request->isPost()) {
            $this->loginForm->setData();
            if($this->loginForm->isValid()) {
                $this->checkLoginData();
            } else {
                $this->loginForm->setValidatorsErrors();
            }
        }

        $this->view->set('loginForm', $this->loginForm);
    }

    private function setLoginForm() {
        $this->view->addHelper('form');

        Loader::loadForm('UserLogin');
        $this->loginForm = new UserLoginForm();
    }

    private function checkLoginData() {
        $username = $this->loginForm->getInputData('username');
        $password = $this->loginForm->getInputData('password');
        $this->model->findByUsername($username);

        if (isset($this->model->id) && $this->model->password == $password) {
            $this->auth->login($this->model);
        } else {
            $this->message->addMessage('username_or_password_error', __USERNAME_OR_PASSWORD_ERROR);
        }
    }

    public function editAction($id = null) {
        if(is_null($id)) {
            $id = $this->auth->loggedUser('id');
        }

        $this->setEditForm();
        if($this->request->isPost()) {
            $this->editForm->setData();
            if($this->editForm->isValid()) {
                $this->saveEditedData();
            } else {
                $this->editForm->setValidatorsErrors();
            }
        } else {
            $this->model->find($id);
            $this->editForm->setData($this->model);
        }
        $this->view->set('editForm', $this->editForm);
    }

    private function setEditForm() {
        $this->view->addHelper('form');

        Loader::loadForm('UserEdit');
        $this->editForm = new UserEditForm();
    }

    private function saveEditedData() {
        $this->model = $this->request->setModelPropertiesFromRequest($this->editForm->getMethod(), $this->model);
        if($this->model->save()) {
            $this->message->addMessage('user_edit_ok', __USER_EDIT_OK);
        } else {
            $this->message->addMessage('user_edit_error', __USER_EDIT_ERROR);
        }
    }

    public function viewAction($id = null) {
        if(is_null($id)) {
            $id = $this->auth->loggedUser('id');
        }

        $this->model->find($id);
        $this->view->set('user', $this->model);
    }

    public function registerAction() {
        $this->setRegisterForm();
        if($this->request->isPost()) {
            $this->registerForm->setData();
            if($this->registerForm->isValid()) {
                $formData = $this->request->getPost();
                $this->model->create($formData);
                if($this->model->save()) {
                    $this->message->addMessage('user_added_ok', __USER_ADD_OK);
                    Router::redirect('User', 'loginAction');
                } else {
                    $this->message->addMessage('user_added_error', __USER_ADD_ERROR);
                }
            } else {
                $this->registerForm->setValidatorsErrors();
            }
        }
        $this->view->set('registerForm', $this->registerForm);
    }

    private function setRegisterForm() {
        $this->view->addHelper('form');

        Loader::loadForm('UserRegister');
        $this->registerForm = new UserRegisterForm();
    }

    public function logoutAction() {
        $this->auth->logout();
    }

    public function indexAction() {
        $users = $this->model->findAll();

        $this->view->set('users', $users);
    }
}