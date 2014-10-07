<?php
/**
 * Klasa zarządzania filtrami używanymi przez elementy formularza.
 *
 * Filtry są odpowiedzialne za przekształcanie zgodnie ze swoimi regułami treści przekazywanymi przez elementy formularza.
 */
class FormFilterComponent {
    public $filters;
    private $filteredInputValue;

    const ADD_SLASHES = 'addSlashes';
    const REMOVE_HTML = 'removeHtml';
    const SQL_WITH_SLASHES = 'sqlWithSlashes';
    const REMOVE_TAGS = 'removeTags';
    const TRIM = 'trim';

    public function __construct($filters) {
        if(!is_array($filters)) {
            $filters = array($filters);
        }
        $this->filters = $filters;
    }

   /**
    * Dodaje filtr do listy filtrów
    *
    * @param string $filter - nazwa dodawanego filtra
    */
    public function addFilter($filter) {
        $this->filters[] = $filter;
    }

    /**
     * Filtruje dane zgodnie z zasadami filtra
     *
     * @param mixed $formInputValue - wartość do przefiltrowania
     * @return mixed przefiltrowana wartość
     */
    public function filter($formInputValue) {
        $this->filteredInputValue = $formInputValue;
        if(!empty($this->filters)) {
            $this->useAllSpecifiedFilters();
        }

        return $this->filteredInputValue;
    }

    /**
     * Wykonuje wszyskie podane filtry na wartości wskazanej do filtrowania
     */
    private function useAllSpecifiedFilters() {
        foreach ($this->filters as $filter) {
            switch ($filter) {
                case self::ADD_SLASHES:

                    $this->addSlashes($this->filteredInputValue);
                    break;
                case self::REMOVE_HTML:

                    $this->removeHtml($this->filteredInputValue);
                    break;
                case self::SQL_WITH_SLASHES:

                    $this->sqlWithSlashes($this->filteredInputValue);
                    break;
                case self::REMOVE_TAGS:

                    $this->removeTags($this->filteredInputValue);
                    break;
                case self::TRIM:

                    $this->trim($this->filteredInputValue);
                    break;
            }
        }
    }

    /**
     * Dodaje back slash przed niebezpieczymi znakami
     * Zabezpiecza przed przedwczesnym zakończeniem ciągu znaków w kodzie
     *
     * @param string $formInputValue - tekst z niebezpiecznymi znakami
     */
    public function addSlashes($formInputValue) {
        $formInputValueWithSlashes = addslashes($formInputValue);

        $this->filteredInputValue = $formInputValueWithSlashes;
    }

    /**
     * Zamienia znaki specjalne na znaki HTML.
     *
     * @param string $formInputValue - tekst ze znakami specialnymi
     */
    public function removeHtml($formInputValue) {
        $formInputValueWithoutHtml = htmlspecialchars($formInputValue);

        $this->filteredInputValue =  $formInputValueWithoutHtml;
    }

    /**
     * Dodaje znaki unikowe do ciągów zawierająceych instrukcje SQL
     *
     * @param string $formInputValue - tekst z kodem SQL
     */
    public function sqlWithSlashes($formInputValue) {
        $formInputValueSqlWithSlashes = mysql_real_escape_string($formInputValue);

        $this->filteredInputValue =  $formInputValueSqlWithSlashes;
    }

    /**
     * Usuwa tagi z tekstu
     *
     * @param string $formInputValue - tekst z tagami
     */
    public function removeTags($formInputValue) {
        $formInputValueWithoutTags = strip_tags($formInputValue);

        $this->filteredInputValue =  $formInputValueWithoutTags;
    }

    /**
     * Usuwa białe znaki z początku i końca tekstu
     *
     * @param string $formInputValue - tekst z białymi znakami na początku i końcu
     */
    public function trim($formInputValue) {
        $trimedFormInputValue = trim($formInputValue);

        $this->filteredInputValue =  $trimedFormInputValue;
    }

    public function getInstance() {

    }
}