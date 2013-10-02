<?php

if (existLoggedUser()) {
    $username = $_SESSION['username'];
    $userId = $_SESSION['userId'];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $pageTitle; ?> | Photobook</title>
        <link rel="stylesheet" href="css/style.css" />
    </head>
    <body>
        <div id="wrapper">
            <header id="header">
                <h1 id="logo">
                    <a href="index.php">photobook</a>
                </h1>
                
                <?php if (existLoggedUser()) { ?>                
                    <div id="hello-user">
                       <p>Hello, <?php echo $username; ?> | <a href="./processing/exit.php">Logout</a></p>
                    </div>                
                <?php } else { ?>
                    <div id="login-form">
                        <form method="POST" action="processing/check-user.php" role="form">
                            <input type="text" name="username" placeholder="username" value="user" required autocomplete="on" />
                            <input type="password" name="password" placeholder="password" value="qwerty" required />
                            <input type="submit" name="user-action" value="Login" />
                        </form>
                    </div>
                <?php } ?>
            </header><!-- #header -->

            <div id="content">
                <?php
                    if (isset($_SESSION['messages'])) {
                        echo $_SESSION['messages'];
                        unset($_SESSION['messages']);
                    }