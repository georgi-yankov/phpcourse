<?php

session_start();

require '../includes/functions.php';

if (!existLoggedUser()) {
    header('Location: ../index.php');
    exit();
}

session_unset();

require '../includes/messages.php';

$_SESSION['messages'] = $messages[4];
header('Location: ../index.php');
exit();