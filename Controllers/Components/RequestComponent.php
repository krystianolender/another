<?php
/**
 * Komponent zarządzający zapytaniami.
 *
 * Daje dostęp do danych przekazywanych w tablicach $_GET i $_POST
 */
class RequestComponent implements Component {
    private $post;
    private $get;

    const POST = 'post';
    const GET = 'get';

    public static function getInstance() {

        return new RequestComponent();
    }

    public function __construct() {
        $this->setGet($_GET);
        $this->setPost($_POST);
    }

    /**
     * Ustawia wartość pobraną ze zmiennej superglobalnej $_POST
     */
    public function setPost() {
        $this->post = $_POST;
    }

    /**
     * Pobiera wartości pobrane wcześniej ze zmiennej superglobalnej $_POST
     *
     * @return array wartości pobrane z $_POST
     */
    public function getPost() {

        return $this->post;
    }

    /**
     * Sprawdza czy za pomocą zmiennej superglobalnej $_POST zostały przesłane wartości i czy były odczytane
     *
     * @return boolean true jeżeli za pomocą $_POST przesłano wartości i były one odczytane
     */
    public function isPost() {
        $this->setPost($_POST);

        if($this->post) {
            $isPost = true;
        } else {
            $isPost = false;
        }

        return $isPost;
    }

    /**
     * Ustawia wartość pobraną ze zmiennej superglobalnej $_GET
     */
    public function setGet() {
        $this->get = $_GET;
    }

    /**
     * Pobiera wartości pobrane wcześniej ze zmiennej superglobalnej $_GET
     *
     * @return array wartości pobrane z $_GET
     */
    public function getGet() {

        return $this->get;
    }

    /**
     * Sprawdza czy za pomocą zmiennej superglobalnej $_GET zostały przesłane wartości i czy były odczytane
     *
     * @return boolean true jeżeli za pomocą $_POST przesłano wartości i były one odczytane
     */
    public function isGet() {
        $this->setGet();

        if($this->get) {
            $isGet = true;
        } else {
            $isGet = false;
        }

        return $isGet;
    }

    /**
     * Sprawdza czy w zmiennych superglobalnych $_GET i $_POST jest wartość o podanej nazwie
     * Jeżeli nie zostanie podany typ superblobalnej to zostaną sprawdzone obydwie
     *
     * @param string $name - nazwa wartości
     * @param string $requestType - typ zmiennej superglobalnej (self::GET/self::POST)
     * @return boolean true jeżeli w zmiennych superglobalnych $_GET lub $_POST istnieje wartość o podanej nazwie
     */
    public function check($name, $requestType = null) {
        $isInRequest = false;

        if($requestType == self::GET) {
            $isInRequest = $this->checkGet($name);
        } elseif($requestType == self::POST) {
            $isInRequest = $this->checkPost($name);
        } else {
            $isInRequest = $this->checkGet($name) || $this->checkPost($name);
        }

        return $isInRequest;
    }

    /**
     * Sprawdza czy w zmiennej superglobalnej $_GET jest wartość o podanej nazwie
     *
     * @param string $name - nazwa wartości
     * @return boolean true jeżeli w zmiennej superglobalnej $_GET jest wartość o podanej nazwie
     */
    public function checkGet($name) {
        $getRequestData = $this->getGet();
        $isInGetRequest = array_key_exists($name, $getRequestData);

        return $isInGetRequest;
    }

    /**
     * Sprawdza czy w zmiennej superglobalnej $_POST jest wartość o podanej nazwie
     *
     * @param string $name - nazwa wartości
     * @return boolean true jeżeli w zmiennej superglobalnej $_POST jest wartość o podanej nazwie
     */
    public function checkPost($name) {
        $postRequestData = $this->getPost();
        $isInPostRequest = array_key_exists($name, $postRequestData);

        return $isInPostRequest;
    }

    public function setModelPropertiesFromRequest($method, $modelObject) {
        $formData = ($method == self::POST) ? $this->getPost() : $this->getGet();
        $modelTableColumns = $modelObject->getTableColumns();
        foreach ($modelTableColumns as $column) {
            if(isset($formData[$column])) {
                $modelObject->$column = $formData[$column];
            }
        }

        return $modelObject;
    }
}