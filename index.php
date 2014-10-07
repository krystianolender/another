<?php
    session_start();

    define('DIRECTORY', str_replace('\\', '/', __DIR__));
    define('ADDRESS', filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
    define('INDEX', 'index.php');
    define('VIEW', DIRECTORY . '/' . 'Views');
    define('LAYOUT', VIEW . '/' . 'Layout');
    define('TMP', DIRECTORY . '/' . 'tmp');
    define('FILES', TMP . '/' . 'files');
    define('WEB', 'web');
    define('IMAGE', WEB . '/' . 'images');

    include_once 'LIB' . '/' . 'misc.php';
    include_once 'LIB' . '/' . 'standard.php';

    require 'web/index.php';
?>