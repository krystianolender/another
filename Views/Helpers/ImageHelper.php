<?php

/**
 * Helper widoku generujący kod HTML dla znacznika IMG.
 *
 * Najważniejszą funkcją jest toHTML() - generuje ona kod HTML znacznika IMG.
 * Helper potrafi sam dodawać właściwości WIDTH i HEIGHT do znacznika IMG na podstawie wyświetlanego pliku.
 */
class ImageHelper {
    private $url;
    private $alt;
    private $title;
    private $width;
    private $height;
    private $html;
    private $image;

    /**
     * Generuje kod HTML dla znacznika IMG
     *
     * @param string $url - adres pliku JPG. Może zawierać opcionalą ścieżkę do pliku i jego rozszerzenie
     * @param string $alt - tekst alternatywny dla pliku graficznego
     * @param string $title - tytuł pliku graficznego
     * @param integer $width - szerokość pliku graficznego w pikselach
     * @param integer $height - wysokość pliku graficznego w pikselach
     * @return type
     */
    public function toHtml($url, $alt = null, $title = null, $width = null, $height = null) {
        Loader::loadComponent('jpeg');
        $this->image = JpegComponent::getInstance();

        $this->setProperties($url, $alt, $title, $width, $height);
        $this->setHtml();

        return $this->html;
    }

    /**
     * Ustawia właściwości pliku graficznego do wyświetlenia
     *
     * @param string $url - adres pliku JPG. Może zawierać opcionalą ścieżkę do pliku i jego rozszerzenie
     * @param string $alt - tekst alternatywny dla pliku graficznego
     * @param string $title - tytuł pliku graficznego
     * @param integer $width - szerokość pliku graficznego w pikselach
     * @param integer $height - wysokość pliku graficznego w pikselach
     */
    private function setProperties($url, $alt = null, $title = null, $width = null, $height = null) {
        $this->setUrl($url);
        $this->setAlt($alt);
        $this->setTitle($title);
        $this->setWidth($width);
        $this->setHeight($height);
    }

    /**
     * Ustawia adres URL dla pliku graficznego uzupełnia go o brakująca elementy (ścieżkę pliku i/lub rozszerzenia) jeżeli nie zostału podane
     *
     * @param string $url - adres URL ze ścieżką i rozszerzeniem
     */
    private function setUrl($url) {
        $this->image->read($url);
        $url = $this->image->getPathNameExtension();

        $this->url = $url;
    }

    /**
     * Ustawia tekst alternatywny dla pliku graficznego
     *
     * @param string $alt - tekst alternatywny dla pliku graficznego
     */
    private function setAlt($alt = null) {

        $this->alt = $alt;
    }

    /**
     * Ustawia tytuł dla pliku graficznego
     * Jeżeli nie jest podana wartość jest ona ustalana na podstawie wartości tekstu alternatywnego pliku graficznego
     *
     * @param string $title - tytuł pliku graficznego
     */
    private function setTitle($title = null) {
        if(!$title) {
            $title = $this->alt;
        }

        $this->title = $title;
    }

    /**
     * Ustawia szerokość pliku graficznrgo.
     * Jeżeli wartość nie jest podana jest ona ustalana z wielkości pliku.
     *
     * @param integer $width - szerokość pliku graficznego w pikselach
     */
    private function setWidth($width = null) {
        if(!$width) {
            $width = $this->image->getWidth();
        }

        $this->width = $width;
    }

    /**
     * Ustawia wysokośc pliku graficznrgo.
     * Jeżeli wartość nie jest podana jest ona ustalana z wielkości pliku.
     *
     * @param integer $height - wysokość pliku graficznego w pikselach
     */
    private function setHeight($height = null) {
        if(!$height) {
            $height = $this->image->getHeight();
        }

        $this->height = $height;
    }

    /**
     * Ustawia kod HTML znacznika IMG dla pliku graficznego
     */
    private function setHtml() {
        $html = '<img src="' . $this->url . '" alt="' . $this->alt . '" title="' . $this->title . '" width="' . $this->width . '" height="' . $this->height . '">';

        $this->html = $html;
    }
}