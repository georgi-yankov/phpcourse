<?php
require 'includes/config.php';
require 'includes/connection.php';
require 'includes/messages.php';
require 'includes/functions.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title><?php printf('%s | %s', $pageTitle, APPLICATION_NAME); ?></title>
        <link rel="stylesheet" href="css/style.css" />
    </head>
    <body>
        <div id="wrapper">
            <header id="header" role="banner">
                <h1 id="logo">
                    <a href="index.php"><?php echo APPLICATION_NAME ?></a>
                </h1>
                
                <div id="search-box">
                    <form method="POST" action="search.php" role="form">
                        <input type="text" name="book-title" placeholder="book title" required />
                        <input type="submit" name="search" value="Search" />
                    </form>
                </div><!-- #search-box -->
            </header><!-- #header -->

            <nav id="main-nav" role="navigation">
                <ul>
                    <li>
                        <a <?php checkForCurrentPage($pageTitle, 'Books') ?> href="index.php">Books</a>
                    </li>
                    <li>
                        <a <?php checkForCurrentPage($pageTitle, 'Add Book') ?> href="add-book.php">Add Book</a>
                    </li>
                    <li>
                        <a <?php checkForCurrentPage($pageTitle, 'Add Author') ?> href="add-author.php">Add Author</a>
                    </li>
                </ul>
            </nav><!-- #main-nav -->

            <div id="content">
                <?php
                if (isset($_SESSION['messages'])) {
                    echo $_SESSION['messages'];
                    unset($_SESSION['messages']);
                }