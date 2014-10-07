<?php

/**
 * Plik - kontener dla akcji ogólnego użytku w aplikacji
 */

/**
 * Zamienia tekst zapisany bez przerw z wielkimi literami na tekst zapisany z podkreśleniami samymi małymi literami
 *
 * @param string $string - tekst zapisany bez przerw z wielkimi literami
 * @return string $uncamelizeString - tekst zapisany z podkreśleniami samymi małymi literami
 */
function uncamelize($string) {
    $stringLetters = str_split($string);
    for ($index = 0; $index < count($stringLetters); $index++) {
        $asciiValueOfCharacter = ord($stringLetters[$index]);
        if ($asciiValueOfCharacter >= 64 && $asciiValueOfCharacter <= 90) {
            $stringLetters[$index] = strtolower($stringLetters[$index]);
            if ($index != 0) {
                $stringLetters[$index] = '_' . $stringLetters[$index];
            }
        }
    }
    $uncamelizeString = implode('', $stringLetters);

    return $uncamelizeString;
}

/**
 * Zamienia tekst zapisany z podkreśleniami samymi małymi literami na tekst zapisany bez przerw wielkimi literami
 *
 * @param string $string - tekst zapisany z podkreśleniami samymi małymi literami
 * @return string - tekst zapisany bez przerw wielkimi literami
 */
function camelize($string) {
    $stringWithoutUnderscore = str_replace('_', ' ', $string);
    $stringFirstWordsLetterWithSpaces = ucwords($stringWithoutUnderscore);
    $stringFirstWordsLetterWithoutSpaces = str_replace(' ', '', $stringFirstWordsLetterWithSpaces);

    return $stringFirstWordsLetterWithoutSpaces;
}

/**
 * Jeżeli parametr jest stringiem otacza go apostrofami.
 * Jeżeli parametr nie jest stringiem zwraca go w niezmienionej formie
 *
 * @param string $value - parametr nie otoczony apostrofami
 * @return type
 */
function addQuotesIfString($value) {
    if(!is_numeric($value) && !is_bool($value) && !is_null($value) && $value != 'null') {

        return '\'' . $value . '\'';
    } else {

        return $value;
    }
}

/**
 * Zamienia tablicę na string w którym słowa są oddzielone podanym separatorem.
 * Filtruje puste wartości w tablicy.
 * Jeżeli tablica jest pusta zwracany string jest pusty.
 *
 * @param array $array - tablica
 * @param string $separator - separator którym mają być oddzielone słowa w wynikowym stringu
 * @return string z wartości z tablicy lub null jeżeli tablica jest pusta
 */
function arrayToString($array, $separator = '') {
    if (!empty($array)) {
        $arrayWithoutEmptyValues = array_filter($array);
        $string = implode($separator, $arrayWithoutEmptyValues);
    } else {
        $string = null;
    }

    return $string;
}

/**
 * Sprawdza czy klasa posiada podaną metodę.
 *
 * @param string $method - nazwa metody
 * @param string $className - nazwa klasy
 * @return boolean true jeżeli klasa posiada daną metodę.
 */
function classHasMethod($method, $className) {
    $classMethods = get_class_methods($className);
    if (in_array($method, $classMethods)) {
        $classHasMethod = true;
    } else {
        $classHasMethod = false;
    }

    return $classHasMethod;
}

/**
 * Sprawdza czy klasa ma daną właściwość
 *
 * @param string $property - nazwa właściwości
 * @param string $className - nazwa klasy
 * @return boolean true jeżeli klasa ma daną właściwość
 */
function classHasProperty($property, $className) {
    $classProperties = get_class_vars($className);
    if (in_array($property, $classProperties)) {
        $classHasProperty = true;
    } else {
        $classHasProperty = false;
    }

    return $classHasProperty;
}

/**
 * Zwraca następujący po podanym element DOM tego samego typu
 *
 * @param DOMElement $domElement - element DOM dla którego ma być odnalezion następujący go element tego samego typu
 * @return DOMElement nasępujący dla podanego element DOM tego samego typu
 */
function getNodeListNextElement(DOMElement $domElement) {
    $find = false;
    $elements = $domElement->parentNode->getElementsByTagName('*');
    foreach ($elements as $element) {
        if ($find) {

            return $element;
        } elseif ($domElement === $element) {
            $find = true;
        }
    }
}

/**
 * Wyświetla tekst pomocny w debugowaniu podanego elementu kodu PHP (zmiennej lub obiektu)
 *
 * @param mixed $objectOrVariables - zmienna lub obiekt do zdebugowania.
 */
function debug($objectOrVariables) {
    echo('<br><br>');

    $backTrace = debug_backtrace();
    $currentInvoke = $backTrace[1];
    echo('Plik: ' . $currentInvoke['file'] . '<br>');
    echo('Klasa: ' . $currentInvoke['class'] . '<br>');
    echo('Funkcja: ' . $currentInvoke['function'] . '<br>');
    echo('Linia: ' . $currentInvoke['line'] . '<br>');

    echo('<pre>');
    var_dump($objectOrVariables);
    echo('</pre>');
}

?>