<?php

session_start();

require '../includes/config.php';
require '../includes/connection.php';
require '../includes/messages.php';
require '../includes/functions.php';

// If a form is not submitted forward the
// user to the index page
if (!isset($_POST['user-action'])) {
    header('Location: ../index.php');
    exit();
}

$userAction = $_POST['user-action'];
$username = $_POST['username'];
$password = $_POST['password'];

$username = safeInput($username);
$password = safeInput($password);

$username = mysqli_real_escape_string($connection, $username);
$password = mysqli_real_escape_string($connection, $password);

switch ($userAction) {
    case 'Login':
        $sql = "SELECT * FROM `users`
                WHERE `name` = '" . $username . "'";

        $query = mysqli_query($connection, $sql);

        if ($query->num_rows == 1) {
            $row = $query->fetch_assoc();

            if ($username == $row['name'] && $password == $row['passwd']) {
                keepDataForLoggedUser($row['user_id'], $row['name'], $row['access_lvl']);
                header('Location: ../index.php');
                exit;
                break;
            }
        }

        $_SESSION['messages'] = $messages['wrongUsernameOrPass'];
        header('Location: ../index.php');
        exit;
        break;
    case 'SignUp':
        $reenterPassword = safeInput($_POST['reenter-password']);

        // Validate data
        validateUsername($username, $messages);
        validatePassword($password, $reenterPassword, $messages);

        if (usernameExist($connection, $username)) {
            $_SESSION['messages'] = $messages['usernameExist'];
            $_SESSION['temp-username'] = $username;
            header('Location: ../sign-up.php');
            exit();
        }

        $sql = "INSERT INTO `users`
                VALUES (NULL, '" . $username . "', '" . $password . "', DEFAULT)";

        $query = mysqli_query($connection, $sql);

        $userId = mysqli_insert_id($connection);

        keepDataForLoggedUser($userId, $username);
        header('Location: ../index.php');
        exit;
        break;
    default:
        $_SESSION['messages'] = $messages['wrongFormSubmission'];
        header('Location: ../index.php');
        exit;
        break;
}