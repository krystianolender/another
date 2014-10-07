<?php

/**
 * Klasa pomocnicza odpowiedzialna za ładowanie innych klas.
 *
 * Odciąża od znajomości ścieżek do pliku przy ładowaniu klas, oraz zabezpiecza przed wielokrotnym załadowaniem klasy.
 */
class Loader {
    const CONTROLLER_FOLDER = 'Controllers';
    const MODEL_FOLDER = 'Models';
    const VIEW_FOLDER = 'Views';
    const HELPER_FOLDER = 'Views/Helpers';
    const COMPONENTS_FOLDER = 'Controllers/Components';
    const FORMS_FOLDER = 'Controllers/Forms';
    const VALIDATE_FOLDER = 'Controllers/Forms/Validate';

    /**
     * Ładuje klasę kontrolera.
     * Kontrolery powinny znajdować się w katalogu którego ścieżka znajduje się w stałej Loader::CONTROLLER_FOLDER
     *
     * @param string $controllerName nazwa kontrolera bez rozszerzenia
     */
    public static function loadController($controllerName) {
        include_once Loader::CONTROLLER_FOLDER . '/Controller.php';
        include_once Loader::CONTROLLER_FOLDER . '/' . $controllerName . '.php';
    }

    /**
     * Ładuję klasę modelu
     * Modele powinny znajdować się w katalogu którego ścieżka znajduje się w stałej Loader::MODEL_FOLDER
     *
     * @param string $modelName nazwa modelu bez rozszerzenia
     */
    public static function loadModel($modelName) {
        include_once Loader::MODEL_FOLDER . '/' . $modelName . '.php';
    }

    /**
     * Ładuje klasę widoku
     */
    public static function loadView() {
        include_once Loader::VIEW_FOLDER . '/View.php';
    }

    /**
     * Ładuje klasę helpera
     * Helper powinien znajdować się w katalogu którego ścieżka znajduje się w stałej Loader::HELPER_FOLDER
     *
     * @param string $helperName - nazwa Helpera bez rozszerzenia
     */
    public static function loadHelper($helperName) {
        include_once DIRECTORY . '/' . Loader::HELPER_FOLDER . '/' . $helperName . '.php';
    }

    /**
     * Ładuje klasę komponentu
     * Komponent powinien znajdować się w katalogu którego ścieżka znajduje się w stałej Loader::COMPONENTS_FOLDER
     *
     * @param string $componentName - nazwa komponentu bez słowa Component i bez rozszerzenia
     */
    public static function loadComponent($componentName = null) {
        $camelizeComponentName = camelize($componentName);
        include_once DIRECTORY . '/' . Loader::COMPONENTS_FOLDER . '/' . $camelizeComponentName . 'Component.php';
    }

    /**
     * Ładuje klasę formularza
     * Formularz powinien znajdować się w katalogu którego ścieżka znajduje się w stałej Loader::FORMS_FOLDER
     *
     * @param string $formName - nazwa formularza bez słowa Form i bez rozszerzenia
     */
    public static function loadForm($formName) {
        $camelizeFormName = camelize($formName);
        include_once DIRECTORY . '/' . Loader::FORMS_FOLDER . '/' . $camelizeFormName . 'Form.php';
    }

    /**
     * Ładuje klasę walidatora
     * Walidator powinien znajdować się w katalogu którego ścieżka znajduje się w stałej Loader::VALIDATE_FOLDER
     *
     * @param string $validateName - nazwa walidatora bez słowa Validate i bez rozszerzenia
     */
    public static function loadValidate($validateName = null) {
        $camelizeValidatorName = camelize($validateName);
        include_once DIRECTORY . '/' . Loader::VALIDATE_FOLDER . '/' . $camelizeValidatorName . 'Validate.php';
    }
}