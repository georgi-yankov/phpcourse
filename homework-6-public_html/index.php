<?php

require realpath(dirname(__FILE__) . '/../homework-6-resources/config.php');
require LIBRARY_PATH . '/basicFunctions.php';
require LIBRARY_PATH . '/templateFunctions.php';

if (isset($_GET['page'])) {
    switch ($_GET['page']) {
        case 'add-book':
            $page = 'add_book';
            break;
        case 'authors':
            $page = 'authors';
            break;
        default:
            $page = 'booklist';
            break;
    }
} else {
    $page = 'booklist';
}

require realpath(dirname(__FILE__) . '/../homework-6-resources/' . $page . '.php');