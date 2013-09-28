<?php

mb_internal_encoding('UTF-8');

/**
 * Check if there is any logged user
 * 
 * @return boolean
 */
function existLoggedUser() {
    if (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true) {
        return true;
    } else {
        return false;
    }
}

/**
 * Normalize data before insert in file
 * 
 * @param string $string
 * @return string
 */
function safeInput($string) {
    $string = trim($string);
    $string = str_replace('|', '', $string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

/**
 * Validate the username and if it's not valid
 * show user a message
 * 
 * @param string $username
 * @param array $messages
 */
function validateUsername($username, $messages) {
    if (mb_strlen($username) < 4 || mb_strlen($username) > 12) {
        $_SESSION['messages'] = $messages[6];
        header('Location: ../index.php');
        exit();
    }

    if (strpos($username, ' ') !== false) {
        $_SESSION['messages'] = $messages[9];
        header('Location: ../index.php');
        exit();
    }
}

/**
 * Validate the password and if it's not valid
 * show user a message
 * 
 * @param string $password
 * @param string $reenterPassword
 * @param array $messages
 */
function validatePassword($password, $reenterPassword, $messages) {
    if (mb_strlen($password) < 6 || mb_strlen($password) > 12 ||
            mb_strlen($reenterPassword) < 6 || mb_strlen($reenterPassword) > 12) {
        $_SESSION['messages'] = $messages[7];
        header('Location: ../index.php');
        exit();
    }

    if ($password !== $reenterPassword) {
        $_SESSION['messages'] = $messages[5];
        header('Location: ../index.php');
        exit();
    }
}

/**
 * Keep data for the logged user in the session
 * 
 * @param string $username
 * @param int $userId
 */
function keepDataForLoggedUser($username, $userId) {
    $_SESSION['isLogged'] = true;
    $_SESSION['username'] = $username;
    $_SESSION['userId'] = $userId;
}

/**
 * Returns the next id of each record
 * 
 * @param array $array
 * @return int
 */
function getNextId($array) {
    if (count($array) == 0) {
        $nextId = 1;
    } else {
        $lastRow = $array[count($array) - 1];
        $lastRowArray = explode('|', $lastRow);
        $lastId = $lastRowArray[0];
        // The id of each new record must be incremented by one
        $nextId = $lastId + 1;
    }

    return $nextId;
}

/**
 * Return human readable file size
 * 
 * @param int $size
 * @return string
 */
function getReadableFileSize($size) {
    if ($size < 1024) { // < 1KB
        $result = $size . ' bytes';
    } else if ($size < (1024 * 1024)) { // < 1MB
        $result = round($size / 1024) . ' KB';
    } else if ($size < (1024 * 1024 * 1024)) { // < 1GB
        $result = round($size / (1024 * 1024), 1) . ' MB';
    } else {
        $result = round(($size / (1024 * 1024 * 1024)), 2) . ' GB';
    }
    
    return $result;
}