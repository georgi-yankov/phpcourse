<?php

session_start();

require './includes/functions.php';

if (existLoggedUser()) {
    header('Location: gallery.php');
    exit();    
}

$pageTitle = 'Sign Up';

require './includes/header.php';

if (isset($_SESSION['messages'])) {
    echo $_SESSION['messages'];
    unset($_SESSION['messages']);
}
?>

<h2><?php echo $pageTitle; ?></h2>

<div id="signup-form">
    <form method="POST" action="processing/check-user.php" role="form">
        <p>
            <label for="username">Username: </label>
            <input id="username" type="text" name="username" required />
        </p>
        <p>
            <label for="password">Password: </label>
            <input id="password" type="password" name="password" required />
        </p>
        <p>
            <label for="reenter-password">Re-enter password: </label>
            <input id="reenter-password" type="password" name="reenter-password" required />
        </p>
        <p>
            <input type="submit" name="user-action" value="SignUp" />
        </p>
    </form>
</div><!-- #signup-form -->

<?php
require './includes/footer.php';