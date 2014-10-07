<?php
/**
 * Komponent do zarządzania plikami.
 *
 * Zapewnia dostęp do podstawowych operacji na plikach (czytanie, wpisywanie, tworzenie, usuwanie i kopiowanie).
 * Medody klasy FileComponent nie pozostawiają otwartych plików
 */
class FileComponent implements Component {
    private $name;
    private $path;
    private $extension;
    private $data;

    protected $fileName;
    protected $defaultPath = FILES;
    protected $defaultExtension = 'txt';
    private $defaultName = 'newFile';

    private static $createMode = 'x+';
    private static $readMode = 'a+';
    private static $writeMode = 'w';

    public static function getInstance() {

        return new FileComponent();
    }

    /**
     * Tworzy plik o podanej nazwie
     * Zamyka plik na końcu działania
     *
     * @param string $fileName - nazwa pliku lub ścieżka dostępu i nazwa pliku
     * @return boolean true jeżeli plik został utworzony
     */
    public function create($fileName = null) {
        $fileHandle = $this->openToCreate($fileName);
        if($fileHandle) {
            $fileCreated = true;
        } else {
            $fileCreated = false;
        }
        fclose($fileHandle);

        return $fileCreated;
    }

    /**
     * Tworzy plik i go otwiera do zapisu
     *
     * @param string $fileName - nazwa pliku lub ścieżka dostępu i nazwa pliku
     * @return zaczep do utworzonego pliku
     */
    private function openToCreate($fileName = null) {

        return $this->open(self::$createMode, $fileName);
    }

    /**
     * Otwiera plik we wskazanym trybie
     * generuje domyślną ścieżkę do pliku jeżeli nie została podana
     * Nazwe pliku można podać samą np. file z rozszerzeniem np. file.txt oraz ze ścierzką dostępu np. files/file.txt
     * jeżeli zabraknie jakiegoś elementu nazwy pliku (ścieżki/nazwy/rozszerzenia) zostanie wygenerowana jego domyślan postać
     *
     * @param type $mode = tryb otwarcia pliku - definiowane w zmiennych statycznych aktualnej klay z sufixem Mode np. self::$createMode
     * @param string $fileName - nazwa pliku lub ścieżka dostępu i nazwa pliku
     * @return zaczep do pliku
     */
    private function open($mode, $fileName = null) {
        if(!$fileName) {
            $fileName = $this->getPathNameExtension();
        }
        $pathNameExtension = $this->defaultPathNameExtension($fileName);
        $fileHandle = fopen($pathNameExtension, $mode);

        return $fileHandle;
    }

    /**
     * Generuje domyślną ścieżkę, nazwę pliku i rozszerzenie, jeżeli jakiś element nie jest podany w parametrze $fileName
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return string $defaultPathNameExtension - ścieżka, nazwa pliku i jego rozszerzenie uzupełnione o brakujące elementy
     */
    public function defaultPathNameExtension($fileName) {
        $this->setProperties($fileName);
        $defaultPathNameExtension = $this->getPathNameExtension();

        return $defaultPathNameExtension;
    }

    /**
     * Otwiera plik i zwraca jego zawartość.
     * Zamyka plik na końcu działania
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return string zawartość pliku
     */
    public function read($fileName) {
        $fileHandle = $this->openToRead($fileName);
        $pathNameExtension = $this->defaultPathNameExtension($fileName);
        $fileSize = filesize($pathNameExtension);
        $this->data = fread($fileHandle, $fileSize);
        fclose($fileHandle);

        return $this->data;
    }

    /**
     * Otwiera plik w trybie do odczytu
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return zaczep do pliku
     */
    private function openToRead($fileName) {

        return $this->open(self::$readMode, $fileName);
    }

    /**
     * Zapisuje dane do wcześniej stworzonego lub otwartego pliku
     *
     * @param string $data dane do zapisania w pliku
     * @return boolean true jeżeli udało sie zapisać dane do pliku
     */
    public function write($data) {
        $fileHandle = $this->openToWrite();
        $written = fwrite($fileHandle, $data);
        if($fileHandle && $written) {
            $saved = true;
        } else {
            $saved = false;
        }
        fclose($fileHandle);

        return $saved;
    }

    /**
     * Otwiera plik do zapisu
     *
     * @return zaczep do otworzonego pliku
     */
    private function openToWrite() {

        return $this->open(self::$writeMode);
    }

    /**
     * Usuwa plik
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return true jeżeli udało się usunąć plik
     */
    public function delete($fileName = null) {
        $pathNameExtension = $this->defaultPathNameExtension($fileName);

        return unlink($pathNameExtension);
    }

    /**
     * Kopiuje zawartość jednego plik do innego
     *
     * @param string $what - nazwa pliku którego zawartość ma byś skopiowana. Może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @param string $where - nazwa pliku do którego zawartość kopiowanego pliku ma być wstawiona. Może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return boolean true jeżeli udało się usunąć plik
     */
    public function copy($what, $where) {

        return copy($what, $where);
    }

    /**
     * Kopiuje zawartość odczytanego wcześniej pliku do pliku wskazanego
     *
     * @param type $where - nazwa pliku którego zawartość ma byś skopiowana. Może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return boolean true jeżeli udało się usunąć plik
     */
    public function copyTo($where) {
        $what = $this->getPathNameExtension();
        $wherePathNameExtension = $this->defaultPathNameExtension($where);

        return $this->copy($what, $wherePathNameExtension);
    }

    /**
     * Zwraca ścieżkę do pliku, jego nazwę i rozszerzenie.
     *
     * @return string nazwa ścieżka, nazwa pliku i jego rozszerzenie
     */
    public function getPathNameExtension() {
        $pathAndNameAndExtension = $this->path . '/' . $this->name . '.' . $this->extension;

        return $pathAndNameAndExtension;
    }

    /**
     * Ustala wartość ścieżki, nazwę i rozszerzenie dla pliku.
     * Jeżeli jakiś element w nazwie pliku nie jest podany zostanie wtedy wygenerowany jego domyślna wartość
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     */
    private function setProperties($fileName) {
        $this->path = $this->setPath($fileName);
        $this->extension = $this->getExtensionFormFileName($fileName);
        $this->name = $this->getName($fileName);
    }

    /**
     * Ustawia ścieżkę do pliku.
     * Jeżeli nie jest podana, zostanie wygenerowana jej domyślna wartość
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return string ścieżka do pliku
     */
    private function setPath($fileName) {
        $path = $this->getPathFromFileName($fileName);
        if(!$path) {
            $path = $this->defaultPath;
        }

        return $path;
    }

    /**
     * Wydziela ścieżkę z nazwy pliku
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return string ścieżka do pliku
     */
    private function getPathFromFileName($fileName) {
        if($this->isPathInFileName($fileName)) {
            $fileNameElements = explode('/', $fileName);
            unset($fileNameElements[count($fileNameElements) - 1]);
            $path = implode('/', $fileNameElements);
        } else {
            $path = null;
        }

        return $path;
    }


    /**
     * Sprawdza czy w nazwie pliki jest ścieżka do pliku
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return boolean true jeżeli w nazwie pliku jest ścieżka
     */
    private function isPathInFileName($fileName) {
        if(strstr('/', $fileName)) {
            $isPathInFileName = true;
        } else {
            $isPathInFileName = false;
        }

        return $isPathInFileName;
    }

    //TODO:: przenieść to do default
    /**
     * Wydziela rozszerzenie z nazwy pliku
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return string rozszerzenie
     */
    private function getExtensionFormFileName($fileName) {
        $extension = null;
        if(strstr($fileName, '.')) {
            $fileNameElements = explode('.', $fileName);
            $lastElement = $fileNameElements[count($fileNameElements)-1];
            if(count($lastElement) >= 1 && count($lastElement) <= 4) {
                $extension = $lastElement;
            }
        } else {
            $extension = $this->defaultExtension;
        }

        return $extension;
    }

    /**
     * Usuwa ścieżkę i rozszerzenie z nazwy pliku
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return string nazwa pliku
     */
    private function getName($fileName) {
        $fileNameWithoutExtension = $this->removeExtension($fileName);
        $filenameWithoutExtensionAndPath = $this->removePath($fileNameWithoutExtension);

        return $filenameWithoutExtensionAndPath;
    }

    /**
     * Usuwa rozszerzenie z nazwy pliku
     *
     * @param string $fileName - może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     * @return nazwa pliku bez rozszerzenia
     */
    private function removeExtension($fileName) {
        $fileNameElements = explode('.', $fileName);
        $elementNumber = count($fileNameElements);
        unset($fileNameElements[$elementNumber - 1]);
        $fileNameWithoutExtension = implode('', $fileNameElements);

        return $fileNameWithoutExtension;
    }

    /**
     * Usuwa ścieżkę z nazwy pliku
     *
     * @param type $fileName
     * @return type
     */
    private function removePath($fileName) {
        $fileNameElements = explode('/', $fileName);
        $numberOfElements = count($fileNameElements);

        $fileNameWithoutPath = $fileNameElements[$numberOfElements - 1];

        return $fileNameWithoutPath;
    }

    /**
     * Ustawia domyślną ścieżkę do pliku
     *
     * @param string $defaultPath - domyślna ścieżka do pliku
     */
    public function setDefaultPath($defaultPath) {

        $this->defaultPath = $defaultPath;
    }

    /**
     * Ustawia domyślne rozszerzenie pliku
     *
     * @param string $defaultExtension - domyslne rozszerzenie pliku
     */
    public function setDefaultExtension($defaultExtension) {

        $this->defaultExtension = $defaultExtension;
    }

    /**
     * Ustawia domyślną nazwę pliku
     *
     * @param string $defaultName
     */
    public function setDefaultName($defaultName) {

        $this->defaultName = $defaultName;
    }
}