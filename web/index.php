<?php
    include_once './Lib/Router.php';
    include_once './Lib/Loader.php';

    $controllerName = Router::getController();
    Loader::loadController($controllerName);
    $controller = new $controllerName;

    $controller->view->auth = $controller->auth;

    $action = Router::getAction();

    Loader::loadComponent('Authorization');
    $authorization = new AuthorizationComponent();
	$controller->$action();
	$controller->view->setMessagesToView();
	$controller->view->render();
?>