<?php

session_start();

if (!isset($_POST['add-author']) &&
    !isset($_POST['add-book']) &&
    !isset($_POST['add-comment'])) {
    
    header('Location: ../index.php');
    exit;
}

require '../includes/config.php';
require '../includes/connection.php';
require '../includes/messages.php';
require '../includes/functions.php';

// Add Author
if (isset($_POST['add-author'])) {
    $authorName = safeInput($_POST['author-name']);

    if (!isValidAuthorName($authorName)) {
        $_SESSION['tempAuthorName'] = $authorName;
        $_SESSION['messages'] = $messages['notValidAuthorName'];
        header('Location: ../add-author.php');
        exit;
    }

    $authorName = mysqli_real_escape_string($connection, $authorName);

    if (authorExistByName($connection, $authorName, $messages)) {
        $_SESSION['tempAuthorName'] = $authorName;
        $_SESSION['messages'] = $messages['authorExist'];
        header('Location: ../add-author.php');
        exit;
    }

    insertAuthor($connection, $authorName, $messages);
}

// Add Book
if (isset($_POST['add-book'])) {
    $bookTitle = safeInput($_POST['book-title']);
    
    foreach ($_POST['authors'] as $value) {
        $authors[] = (int) $value;
    }
    
    if (!isValidBookTitle($bookTitle)) {
        $_SESSION['tempBookTitle'] = $bookTitle;
        $_SESSION['tempAuthors'] = $authors;
        $_SESSION['messages'] = $messages['notValidBookTitle'];
        header('Location: ../add-book.php');
        exit;
    }
    
    foreach ($authors as $value) {
        if (!authorExistById($connection, $value, $messages)) {
            $_SESSION['tempBookTitle'] = $bookTitle;
            $_SESSION['tempAuthors'] = $authors;
            $_SESSION['messages'] = $messages['notValidAuthorId'];
            header('Location: ../add-book.php');
            exit;
        }
    }
    
    $bookTitle = mysqli_real_escape_string($connection, $bookTitle);
    
    if (bookExistByName($connection, $bookTitle, $messages)) {
            $_SESSION['tempBookTitle'] = $bookTitle;
            $_SESSION['tempAuthors'] = $authors;
            $_SESSION['messages'] = $messages['bookExist'];
            header('Location: ../add-book.php');
            exit;
    }
    
    insertBook($connection, $bookTitle, $authors, $messages);
}

// Add Comment
if (isset($_POST['add-comment'])) {
    $bookId = (int) $_POST['book-id'];
    $comment = safeInput($_POST['comment-txt']);
    
    if (!bookExistById($connection, $bookId, $messages)) {
        $_SESSION['messages'] = $messages['bookNotExist'];
        header('Location: ../book.php');
        exit;
    }

    if (!isValidComment($comment)) {
        $_SESSION['tempComment'] = $comment;
        $_SESSION['messages'] = $messages['notValidComment'];
        header('Location: ../book.php?book=' . $bookId);
        exit;
    }
    
    insertComment($connection, $bookId, $_SESSION['userId'], $comment, $messages);
}