<?php

session_start();

require '../includes/functions.php';
require '../includes/messages.php';

// If a form is not submitted forward the
// user to the index page
if (!isset($_POST['user-action'])) {
    header('Location: ../index.php');
    exit();
}

$userAction = $_POST['user-action'];
$username = $_POST['username'];
$password = $_POST['password'];

// Check for empty fields
if (trim($username) == '' || trim($password) == '' ||
        (isset($_POST['reenter-password']) && trim($_POST['reenter-password']) == '')) {
    $_SESSION['messages'] = $messages[1];
    header('Location: ../index.php');
    exit();
}

$usersDataFile = '../data/users.txt';

if (file_exists($usersDataFile)) {
    $allUsers = file($usersDataFile);
}

switch ($userAction) {
    case 'Login':
        if (!file_exists($usersDataFile)) {
            $_SESSION['messages'] = $messages[2];
            header('Location: ../index.php');
            exit();
        }

        $userExist = false;

        foreach ($allUsers as $value) {
            $currentUser = explode('|', $value);
            if (($username == $currentUser[1]) && (encryptPassword($password) == trim($currentUser[2]))) {
                $userExist = true;
                break;
            }
        }

        if ($userExist) {
            keepDataForLoggedUser($username, $currentUser[0]);
            header('Location: ../gallery.php');
            exit();
        } else {
            $_SESSION['messages'] = $messages[2];
            header('Location: ../index.php');
            exit();
        }
        break;
    case 'SignUp':
        // Normalize data
        $username = safeInput($username);
        $password = safeInput($password);
        $reenterPassword = safeInput($_POST['reenter-password']);

        // Validate data
        validateUsername($username, $messages);
        validatePassword($password, $reenterPassword, $messages);

        if (file_exists($usersDataFile)) {
            $userId = getNextId($allUsers);            
            // Check if already exist user with
            // the same username
            if ($userId > 0) {
                foreach ($allUsers as $value) {
                    $currentUser = explode('|', $value);
                    if ($username == $currentUser[1]) {
                        $_SESSION['messages'] = $messages[8];
                        header('Location: ../index.php');
                        exit();
                    }
                }
            }

            // Insert new user
            $dataToInsert = $userId . '|' . $username . '|' . encryptPassword($password) . "\n";
            file_put_contents($usersDataFile, $dataToInsert, FILE_APPEND);

            // Create directories for the new user
            $newMainDirName = 'user-' . $userId;
            $path = realpath('../');
            $newPath = $path . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $newMainDirName;
            mkdir($newPath);

            $newThumbsDirName = $newPath . DIRECTORY_SEPARATOR . 'thumbs';
            mkdir($newThumbsDirName);

            keepDataForLoggedUser($username, $userId);
            header('Location: ../gallery.php');
            exit();
        }
        break;
    default:
        $_SESSION['messages'] = $messages[3];
        header('Location: ../index.php');
        exit();
        break;
}