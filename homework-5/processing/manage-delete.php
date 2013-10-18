<?php

session_start();

if (!isset($_SESSION['accessLevel']) || $_SESSION['accessLevel'] < 2) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['book'])) {
    header('Location: ../index.php');
    exit;
}

require '../includes/config.php';
require '../includes/connection.php';
require '../includes/messages.php';
require '../includes/functions.php';

// Delete Book
$bookId = (int) $_GET['book'];
$bookId = mysqli_real_escape_string($connection, $bookId);

if (!bookExistById($connection, $bookId, $messages)) {
    $_SESSION['messages'] = $messages['bookNotExist'];
    header('Location: ../index.php');
    exit;
}

deleteBook($connection, $bookId, $messages);