<?php

mb_internal_encoding('UTF-8');

/**
 * Check if there is any logged user
 * 
 * @return boolean
 */
function existLoggedUser() {
    $isUserLogged = false;

    if (isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == true) {

        $diff = time() - $_SESSION['time'];
        $maxTime = 600; // 10 min

        if ($diff <= $maxTime) {
            $_SESSION['time'] = time(); // time reset
            $isUserLogged = true;
        } else {
            session_unset();
        }
    }

    return $isUserLogged;
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
    $_SESSION['time'] = time();
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

/**
 * Downloads a file
 * 
 * @param string $fullPath
 */
function downloadFile($fullPath) {

    // Must be fresh start 
    if (headers_sent()) {
        die('Headers Sent');
    }

    // Required for some browsers 
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }

    // File Exists? 
    if (file_exists($fullPath)) {

        // Parse Info / Get Extension 
        $fsize = filesize($fullPath);
        $path_parts = pathinfo($fullPath);
        $ext = strtolower($path_parts["extension"]);

        // Determine Content Type 
        switch ($ext) {
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "exe":
                $ctype = "application/octet-stream";
                break;
            case "zip":
                $ctype = "application/zip";
                break;
            case "doc":
                $ctype = "application/msword";
                break;
            case "xls":
                $ctype = "application/vnd.ms-excel";
                break;
            case "ppt":
                $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            default:
                $ctype = "application/force-download";
        }

        header("Pragma: public"); // required 
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers 
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $fsize);
        ob_clean();
        flush();
        readfile($fullPath);
    } else {
        die('File Not Found');
    }
}

/**
 * Encrypt a password
 * 
 * @param string $password
 * @return string
 */
function encryptPassword($password) {
    $result = base64_encode(str_rot13($password));
    $result = substr($result, 1, -1);
    $result = sha1($result);
    return $result;
}