<?php

/**
 * Główna klasa modeli.
 *
 * Definiuje model wzorca MVC i obsługuje mechanizmy dostępu i zarządzania danymi w bazie danych za pomocą wzorca Active record.
 * Klasy dziedziczące po klasie Model automatycznie są wiązane z tabelą w bazie danych odpowiadającą im nazwą.
 * Obiekt klasy dziedziczącej po klasie Model reprezentuje jeden wiersz powiązanej z nim tabeli bazy danych.
 */
include_once './Lib/misc.php';

class Model {
    const FIND_MAGIC_WORD = 'findBy';
    const FIND_ALL_MAGIC_WORD = 'findAllBy';

    private $className = null;
    private $databaseHost = 'localhost';
    private $databaseName = 'another';
    private $databaseUser = 'admin';
    private $databasePassword = 'admin';
    private $databaseConnection = null;

    public function __construct() {
        $this->className = get_class($this);
        $this->databaseConnection = $this->connectToDatabase($this->databaseHost, $this->databaseName, $this->databaseUser, $this->databasePassword);

        $this->addPropertiesToCurrentModel();
    }

    public function create($data) {
        $modelColums = $this->getTableColumnsWithoutId();
        foreach ($modelColums as $modelColum) {
            if(isset($data[$modelColum])) {
                $this->$modelColum = $data[$modelColum];
            }
        }
    }

    /**
     * Tworzy połączenie do bazy danych.
     *
     * @param string $host - nazwa hosta
     * @param string $database - nazwa bazy danych
     * @param string $user - nazwa użytkownika bazy danych
     * @param string $password - hasło użytkownika bazy danych
     * @return \PDO
     */
    public function connectToDatabase($host, $database, $user, $password) {
        try {
            $databaseConnection = new PDO("pgsql:host = $host; dbname = $database", $user, $password);
        } catch (PDOException $e) {
            print "Problem z połaczeniem do bazy danych";
            die();
        }

        return $databaseConnection;
    }

    /**
     * Dodaje właściwości do obiektu modelu zgodne z kolumnami jego tabeli w bazie danych
     */
    private function addPropertiesToCurrentModel() {
        $columnsWithoutId = $this->getTableColumnsWithoutId();

        foreach ($columnsWithoutId as $column) {
            $this->$column = null;
        }
    }

    /**
     * Pobiera dla modelu nazwy kolumn poza id z tabeli bazy danych
     *
     * @return array nazwy kolumn tabeli modelu poza id
     */
    private function getTableColumnsWithoutId() {
        $columns = $this->getTableColumns();
        foreach ($columns as $key => $column) {
            if($column == 'id') {
                unset($columns[$key]);
            }
        }
        $columnsWithoutId = array_values($columns);

        return $columnsWithoutId;
    }

    /**
     * Pobiera dla modelu nazwy kolumn z tabeli bazy danych
     *
     * @return array nazwy kolumn tabeli modelu
     */
    public function getTableColumns() {
        $query = $this->databaseConnection->query('SELECT * FROM ' . $this->model . ' limit 0');

        $columnNumber = $query->columnCount();
        for ($i = 0; $i < $columnNumber; $i++) {
            $column = $query->getColumnMeta($i);
            $columns[] = $column['name'];
        }

        return $columns;
    }

    /**
     * Ustawia właściwości modelu danymi odczytanymi z wiersza o podanym identyfikatorze z tabeli przypisanej modelowi.
     *
     * @param integer $id - identyfikator wiersza z bazy danych.
     * @return boolean true jeżeli udało się ustawić właściwości modelu danymi z wiersza z bazy danych
     */
    public function find($id) {

        return $this->findBy('id', $id);
    }

    /**
     * Ustawia właściwości modelu danymi odczytanymi z pierwszego wiersza o podanej wartości wskazanej kolumny z tabeli przypisanej modelowi.
     *
     * @param string $column - nazwa kolumny po której ma być wyszukiwany wiersz w tabeli
     * @param string $value - wartość poszukiwana w podanej kolumnie bazy danych
     * @return boolean true jeżeli udało się ustawić właściwości modelu danymi z wiersza z bazy danych
     */
    private function findBy($column, $value) {
        $findResults = $this->query($column, $value, 1);
        $objectsFromDatabase = $findResults->fetchAll(PDO::FETCH_CLASS);

        $finded = ($objectsFromDatabase) ? true : false;
        if($finded) {
            $objectFromDatabase = array_shift($objectsFromDatabase);
            $this->setPropertiesToCurrentObject($objectFromDatabase);
        }

        return $finded;
    }

    /**
     * Ustawia właściwości obiektu modelu danymi pobranymi z bazy danych
     *
     * @param Model $objectFromDatabase - obiekt utworzony z danych z bazy dany
     */
    private function setPropertiesToCurrentObject($objectFromDatabase) {
        $propertiesObjectFromDatabase = get_object_vars($objectFromDatabase);
        foreach ($propertiesObjectFromDatabase as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Pobiera wszystkie wiersze z tabeli przypisanej do modelu w formie tablicy obiektów
     *
     * @return array tablica obiektów z wartościami ich włąściwości pobranymi z bazy danych
     */
    public function findAll() {

        return $this->findAllBy(null);
    }

    /**
     * Pobiera wszystkie wiersze z tabeli przypisanej do modelu dla których podana kolumna ma wskazaną wartość.
     *
     * @param string $column - nazwa kolumny tabeli przypisanej do bazy danych po której mają być wyszukiwane wiersze
     * @param mixed $value - poszukiwana wartość kolumny
     * @return array tablica obiektów z wartościami ich włąściwości pobranymi z bazy danych
     */
    private function findAllBy($column = null, $value = null) {
        $findResults = $this->query($column, $value);
        $objectsFromDatabase = $findResults->fetchAll(PDO::FETCH_CLASS);

        return $objectsFromDatabase;
    }

    /**
     * Wykonuje zapytanie SQL na tabeli przypisanej do modelu
     *
     * @param string $column - nazwa kolumny bazy danych po której są wyszukiwane wiersze
     * @param mixed $value - poszukiwana wartość kolumny
     * @param integer $limit - ilość wierszy po przekroczeniu której kolejne wiersze nie będą wyszukiwane
     * @return obiekt PDOStatement albo false jeżeli ani jeden wiersz nie zostanie odnaleziony
     */
    private function query($column = null, $value = null, $limit = null) {
        $query = 'SELECT * FROM ' . $this->model;
        $queryWithWhere = $this->addWhereToQuery($query, $column, $value);
        $queryWithWhereAndLimit = $this->addLimitToQuery($queryWithWhere, $limit);

        $findResults = $this->databaseConnection->query($queryWithWhereAndLimit);

        return $findResults;
    }

    /**
     * Dodaje WHERE do zapytania SQL
     *
     * @param string $query - zapytanie SQL bez WHERE
     * @param string $column - nazwa kolumny bazy danych po której są wyszukiwane wiersze
     * @param mixed $value - poszukiwana wartość kolumny
     * @return string $query - zapytanie SQL uzupełnione o WHERE
     */
    private function addWhereToQuery($query, $column, $value) {
        if ($column && $value) {
            $column = uncamelize($column);
            $value = addQuotesIfString($value);
            $query .= ' WHERE ' . $column . ' = ' . $value;
        }

        return $query;
    }

    /**
     * Dodaje LIMIT do zapytania SQL
     *
     * @param string $query - zapytanie SQL bez WHERE
     * @param integer $limit - ilość wierszy po przekroczeniu którego kolejne wiersze nie są wyszukiwane
     * @return string $query - zapytanie SQL uzupełnione o LIMIT
     */
    private function addLimitToQuery($query, $limit) {
        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }

        return $query;
    }

    /**
     * Metoda magiczna pozwalająca wywoływać metody zgodnie ze schematem: findByNazwaKolumny($value)
     * Słowo występujące po findBy powinny być nazwą kolumny w tabeli przypisanej do modelu zapisaną w formacie CamelCase
     * $value to poszukiwana wartość kolumny
     *
     * @param string $name - nazwa nieistniejącej metody
     * @param mixed $arguments - parametr nieistniejącej metody
     * @return array tablica obiektów z wartościami ich włąściwości pobranymi z bazy danych
     */
    public function __call($name, $arguments) {
        $value = array_shift($arguments);

        if (strstr($name, Model::FIND_MAGIC_WORD)) {
            $findMagicWordLenth = strlen(Model::FIND_MAGIC_WORD);
            $column = substr($name, $findMagicWordLenth);

            return $this->findBy($column, $value);
        } elseif (strstr($name, Model::FIND_ALL_MAGIC_WORD)) {
            $findAllMagicWordLenth = strlen(Model::FIND_ALL_MAGIC_WORD);
            $column = substr($name, $findAllMagicWordLenth);

            return $this->findAllBy($column, $value);
        }
    }

    /**
     * Zapisuje aktualne wartości właściwości modelu do bazy danych
     * Jeżeli w aktualnym obiekcie modelu istnieje właściwość id i jest ona nie pusta to aktualizowany jest wiersz w tabeli zgodny z podanym identyfikatorem.
     * Jeżeli w aktualnym obiekcie modelu nie istnieje włąściwość id lub jest ona pusta to wstawiany jest nowy wiersz do bazy danych.
     *
     * @return obiekt PDOStatement lub false jeżeli operacja zapisania danych do bazy się nie powiodła
     */
    public function save() {
        if (isset($this->id)) {

            return $this->update();
        } else {

            return $this->insert();
        }
    }

    /**
     * Nadpisuje istnejący wiersz w tabeli bazy danych przypidanej do modelu o podanym identyfikatorze wartościami przechowywanymi we włąściwościach modelu
     *
     * @return obiekt PDOStatement lub false jeżeli operacja zapisania danych do bazy się nie powiodła
     */
    private function update() {
        $columns = $this->getTableColumns();
        $columnsAndNewValues = $this->prepareValuesToColumns($columns);
        $update = 'UPDATE ' . $this->model . ' SET ' . $columnsAndNewValues . ' WHERE id = ' . $this->id;

        $query = $this->databaseConnection->query($update);

        return $query;
    }

    /**
     * Tworzy fragment SET dla kodu SQL w formie kolumna1 = wartość1, kolumna2 = wartość2
     * Wartościami są aktualne wartości włąściwości obiektu modelu posiadające swoje odpowiedniki w kolumnach bazy danych
     *
     * @param array $columns - nazwy kolumn tabeli bazy danych przypisanej do modelu
     * @return string $columsAndNewValues - fragment SET zapytania UPDATE kodu SQL
     */
    private function prepareValuesToColumns($columns) {
        $columsAndNewValuesElements = array();
        foreach ($columns as $column) {
            if(!empty($this->$column)) {
                $value = addQuotesIfString($this->$column);
                $columsAndNewValuesElements[] = $column . ' = ' . $value;
            }
        }
        $columsAndNewValues = implode(', ', $columsAndNewValuesElements);

        return $columsAndNewValues;
    }

    /**
     * Tworzy nowy wiersz w tabeli bazy danych przypisanej do modelu z wartościami przechowywanymi we włąściwościach modelu
     *
     * @return obiekt PDOStatement lub false jeżeli operacja zapisania danych do bazy się nie powiodła
     */
    private function insert() {
        $insert = 'INSERT INTO ' . $this->model;
        $columnsWithoutId = $this->getTableColumnsWithoutId();
        $values = $this->prepareValuesToInsert($columnsWithoutId);

        $columnsAndValues = array_combine($columnsWithoutId, $values);
        $columnsAndValuesWithoutNulls = array_filter($columnsAndValues);

        $insertWithInto = $insert . '(' . implode(',', array_keys($columnsAndValuesWithoutNulls)) . ')';
        $insertWithIntoAndValues = $insertWithInto . ' VALUES (' . implode(',', array_values($columnsAndValuesWithoutNulls)) .')';

        $query = $this->databaseConnection->exec($insertWithIntoAndValues);
        $querySuccess = ($query != FALSE) ? true : false;

        return $querySuccess;
    }

    /**
     * Tworzy fragment VALUES dla kodu SQL
     * Wartościami są aktualne wartości włąściwości obiektu modelu posiadające swoje odpowiedniki w kolumnach bazy danych
     *
     * @param array $columns - nazwy kolumn tabeli bazy danych przypisanej do modelu
     * @return string $columsAndNewValues - fragment SET zapytania UPDATE kodu SQL
     */
    private function prepareValuesToInsert($columns) {
        $values = array();
        foreach ($columns as $column) {
            if(is_null($this->$column)) {
                $value = null;
            } else {
                $value = $this->$column;
            }
            $values[] = addQuotesIfString($value);
        }

        return $values;
    }

    /**
     * Sprawdza czy użytkownik o podanym identyfikatorze jest właścicielem obiektu zapisanego w wierszu o podanym id tabeli przypisanej do modelu
     *
     * @param integer $id - identyfikator wiersza tabeli przypisanej do modelu
     * @param integer $userId - identyfikator użytkownika
     * @return boolean
     */
    public function userIsOwner($id, $userId) {
        $userIsOwner = false;
        if($this->tableHasOwnerIdColumn()) {
            $this->find($id);
            if($this->id && $this->owner_id == $userId) {
                $userIsOwner = true;
            }
        }

        return $userIsOwner;
    }

    /**
     * Sprawdza czy tabela przypisana do modelu posiada kolumnę (owner_id) określającą identyfikator właściciela zaisanego w niej obiektu
     *
     * @return boolean true jeżeli tabela posiada kolumnę owner_id
     */
    public function tableHasOwnerIdColumn() {

        return modelHasColumn('owner_id');
    }

    /**
     * Sprawdza czy tabela przypisana do modelu posiada wskazaną kolumnę
     *
     * @param string $column - nazwa kolumny
     * @return boolean true jeżeli tabela posiada wskazaną kolumnę
     */
    public function modelHasColumn($column) {
        $tableColumn = $this->getTableColumns();
        if(in_array($column, $tableColumn)) {
            $modelHasColumn = true;
        } else {
            $modelHasColumn = false;
        }

        return $modelHasColumn;
    }

    public function hasAnyBy($column, $value){
        $result = $this->findAllBy($column, $value);
        if($result) {

            return false;
        } else {

            return true;
        }
    }
}