<?php
Loader::loadComponent('file');

/**
 * Komponent zarządzania plikami JPG.
 *
 * Rozszerza możliwości obsługi plików klasy FileComponent o czytanie zawartości plików JPG wykonywanie na nich operacji graficznych (skalowanie).
 * Za wyświetlanie plików JPG odpowiedzialna jest klasa ImageHelper
 */
class JpegComponent {
    protected $defaultExtension = 'jpg';
    protected $defaultPath = IMAGE;
    private $imageResource;
    private $pathNameExtension;
    private $file;
    private $fileClassName;

    public static function getInstance() {
        Loader::loadComponent('File');
        $jpegComponent = new JpegComponent();

        return $jpegComponent;
    }

    public function __construct() {
        $this->file = FileComponent::getInstance();
        $this->file->setDefaultPath($this->defaultPath);
        $this->file->setDefaultExtension($this->defaultExtension);
        $this->fileClassName = get_class($this->file);
    }

    /**
     * Czyta plik jpg
     * Odczytuje i ustawia image dla podanego pliku jpg
     * Jeżeli nazwa pliku nie posiada ścieżki i/lub rozszerzenia będą one ustawione zgodnie z wartościami domyślnymi
     *
     * @param String $fileName - nazwa pliku graficznego z opcionalną ścieżką i rozszerzeniem
     */
    public function read($fileName) {
        $this->pathNameExtension = $this->file->defaultPathNameExtension($fileName);
        $this->imageResource = imagecreatefromjpeg($this->pathNameExtension);
    }

    /**
     * Kopiuje zawartość odczytanego wcześniej pliku jpg do pliku wskazanego
     *
     * @param type $where - nazwa pliku jpg którego zawartość ma byś skopiowana. Może zawierać ścieżkę, nazwę pliku lub jego rozszerzanie, albo dowolną ich kombinację
     */
    public function copyTo($where) {
        $copied = $this->file->copyTo($where);
        if($copied) {
            $this->read($where);
        }
    }

    /**
     * Zmienia rozmiar otwartego wcześniej pliku graficznego
     *
     * @param integer $newWidth - nowa szerokość w pikselach
     * @param integer $newHeight - nowa wysokość w pikselach
     */
    public function resize($newWidth, $newHeight) {
        list($oldWidth, $oldHeight) = getimagesize($this->pathNameExtension);
        $imageInNewSize = imagecreatetruecolor($newWidth, $newHeight);
        $imaginInOldSize = $this->pathNameExtension;
        imagecopyresampled($imageInNewSize, $imaginInOldSize, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
        $this->imageResource = $imageInNewSize;
    }

    /**
     * Zapisuje dane pliku graficznego do wcześniej otwartego pliku
     *
     * @param integer $quality - jakość pliku jpg w %
     * @return boolean true jeżeli udało się zapisać dane w pliku graficznym
     */
    public function write($quality = 75) {
        $imageWritten = imagejpeg($this->imageResource, $this->pathNameExtension, $quality);
        imagedestroy($this->imageResource);

        return $imageWritten;
    }

    /**
     * Zapisuje dane z jednego pliku jpg do drugiego pliku jpg w podanym rozmiarze i podanej jakości
     *
     * @param string $oldFileName - nazwa pliku jpg który ma być zapisany w nowym rozmiarze. Jeżeli nazwa pliku nie zawiera ścieżki i rozszerzenia będzie on uzupełniony w wartościami domyślnymi.
     * @param string $newFileName - nazwa pliku jpg do którego ma być zapisane dane z pliku źródłowego w nowym rozmiarze.  Jeżeli nazwa pliku nie zawiera ścieżki i rozszerzenia będzie on uzupełniony w wartościami domyślnymi.
     * @param integer $newWidth - szerokość pliku wynikowego
     * @param integer $newHeight - wysokość pliku wynikowego
     * @param integer $quality - jakość wynikowego pliku jpg w %
     */
    public function writeInNewSize($oldFileName, $newFileName, $newWidth, $newHeight, $quality = 75) {
        $this->read($oldFileName);
        $this->copyTo($newFileName);
        $this->resize($newWidth, $newHeight);
        $this->write($quality);
    }

    /**
     * Zwraca właściwości tagu HTML z wysokością i szerokością pliku jpg
     * Otrzymywany ciąg znaków może być używany bezpośrednio w kodzie HTML
     *
     * @return string fragment kodu HTML z szerokością i wysokością dla znacznika img
     */
    public function getHtmlImageTagSize() {
        $imageSize = $this->getSize();
        $htmlImageTagSize = $imageSize[3];

        return $htmlImageTagSize;
    }

    /**
     * Zwraca tablicę rozmiarów pliku jpg
     *
     * @return array tablica rozmiarów pliku jpg
     */
    public function getSize() {
        $imageSize = getimagesize($this->pathNameExtension);

        return $imageSize;
    }

    /**
     * Pobiera szerokość pliku jpg w pikselach
     *
     * @return integer $width - szerokość w pikselach
     */
    public function getWidth() {
        $imageSize = getimagesize($this->pathNameExtension);
        $width = $imageSize[0];

        return $width;
    }

    /**
     * Pobiera wysokość pliku jpg w pikselach
     *
     * @return integer $height - wysokość w pikselach
     */
    public function getHeight() {
        $imageSize = getimagesize($this->pathNameExtension);
        $height = $imageSize[1];

        return $height;
    }

    /**
     * Pobiera ścieżkę do pliku, jego nazwę i rozszerzenie
     *
     * @return type
     */
    public function getPathNameExtension() {

        return $this->file->getPathNameExtension();
    }
}