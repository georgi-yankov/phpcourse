<?php

session_start();

require 'includes/config.php';
require 'includes/connection.php';
require 'includes/messages.php';
require 'includes/functions.php';

if (existLoggedUser()) {
    $username = $_SESSION['username'];
    $userId = $_SESSION['userId'];
}
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
                <?php if (existLoggedUser()) { ?>                
                    <div id="hello-user">
                        <p>Hello, <?php echo $username . ' ' . printAccessLevelName($connection, $_SESSION['accessLevel']); ?> | <a href="processing/logout.php">Logout</a></p>
                    </div>                
                <?php } else { ?>
                    <div id="login-form">
                        <form method="POST" action="processing/check-user.php" role="form">
                            <input type="text" name="username" placeholder="username" required autocomplete="on" />
                            <input type="password" name="password" placeholder="password" required />
                            <input type="submit" name="user-action" value="Login" /> or <a href="sign-up.php">Sign Up</a>
                        </form><!-- #login-form -->
                    </div>
                <?php } ?>
                
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
                    <?php if (existLoggedUser()) { ?>
                        <li>
                            <a <?php checkForCurrentPage($pageTitle, 'Account') ?> href="account.php">Account</a>
                        </li>
                    <?php } ?>
                </ul>
            </nav><!-- #main-nav -->

            <div id="content">
                <?php
                if (isset($_SESSION['messages'])) {
                    echo $_SESSION['messages'];
                    unset($_SESSION['messages']);
                }