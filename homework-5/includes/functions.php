<?php

mb_internal_encoding('UTF-8');

/**
 * Return class "current" if it is in that page
 * 
 * @param string $pageTitle
 * @param string $currentLink
 * @return string/void
 */
function checkForCurrentPage($pageTitle, $currentLink) {
    if ($pageTitle == $currentLink) {
        echo 'class="current"';
    }
}

/**
 * Normalize data
 * 
 * @param string $string
 * @return string
 */
function safeInput($string) {
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

function isValidAuthorName($authorName) {
    if (mb_strlen($authorName) < 3 || mb_strlen($authorName) > 50) {
        return false;
    } else {
        return true;
    }
}

function isValidBookTitle($bookTitle) {
    if (mb_strlen($bookTitle) < 3 || mb_strlen($bookTitle) > 100) {
        return false;
    } else {
        return true;
    }
}

function isValidComment($comment) {
    if (mb_strlen($comment) < 3 || mb_strlen($comment) > 500) {
        return false;
    } else {
        return true;
    }
}

function authorExistByName($connection, $authorName, $messages) {
    $sql = "SELECT `author_name`
            FROM `authors`
            WHERE `author_name` = '$authorName'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../add-author.php');
        exit;
    }

    $result = $query->num_rows;

    if ($result > 0) {
        return true;
    } else {
        return false;
    }
}

function authorExistById($connection, $authorId, $messages) {
    $sql = "SELECT `author_id`
            FROM `authors`
            WHERE `author_id` = '$authorId'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../add-author.php');
        exit;
    }

    $result = $query->num_rows;

    if ($result > 0) {
        return true;
    } else {
        return false;
    }
}

function bookExistByName($connection, $bookTitle, $messages) {
    $sql = "SELECT `book_title`
            FROM `books`
            WHERE `book_title` = '$bookTitle'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    $result = $query->num_rows;

    if ($result > 0) {
        return true;
    } else {
        return false;
    }
}

function bookExistById($connection, $bookId, $messages) {
    $sql = "SELECT `book_id`
            FROM `books`
            WHERE `book_id` = ?";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $bookId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        return true;
    } else {
        return false;
    }
}

function authorHasBooks($connection, $authorId, $messages) {
    $sql = "SELECT `author_id`
            FROM `books_authors`
            WHERE `author_id` = ?";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $authorId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        return true;
    } else {
        return false;
    }
}

function getAllAuthors($connection, $messages, $currentSort = 'ASC') {
    $sql = "SELECT *
            FROM `authors`
            ORDER BY `author_name` $currentSort";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../add-author.php');
        exit;
    }

    $allAuthorsData = array();

    while ($row = $query->fetch_assoc()) {
        $allAuthorsData['authorId'][] = $row['author_id'];
        $allAuthorsData['authorName'][] = $row['author_name'];
    }

    return $allAuthorsData;
}

function insertAuthor($connection, $authorName, $messages) {
    $sql = "INSERT INTO `authors`
            VALUES (NULL, '$authorName')";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../add-author.php');
        exit;
    }

    $_SESSION['messages'] = $messages['authorInserted'];
    header('Location: ../add-author.php');
    exit;
}

function insertBook($connection, $bookTitle, $authors, $messages) {
    $sql = "INSERT INTO `books`
            VALUES (NULL, '$bookTitle')";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../add-book.php');
        exit;
    }

    $lastBookInsertedId = mysqli_insert_id($connection);

    foreach ($authors as $value) {
        $values[] = "($lastBookInsertedId, $value)";
    }

    $sql = "INSERT INTO `books_authors`
            VALUES " . implode(',', $values) . "";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../add-book.php');
        exit;
    }

    $_SESSION['messages'] = $messages['bookInserted'];
    header('Location: ../add-book.php');
    exit;
}

function insertComment($connection, $bookId, $userId, $comment, $messages) {
    $sql = 'INSERT INTO `comments`
            VALUES (NULL, ?, ?, ?, NOW())';

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'iis', $bookId, $userId, $comment);
    mysqli_stmt_execute($stmt);

    $_SESSION['messages'] = $messages['commentInserted'];
    header('Location: ../book.php?book=' . $bookId);
    exit;
}

function deleteBook($connection, $bookId, $messages) {
    $sql = "DELETE FROM `books`
            WHERE `book_id` = '$bookId'
            LIMIT 1";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    $sql = "DELETE FROM `books_authors`
            WHERE `book_id` = '$bookId'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    $sql = "DELETE FROM `comments`
            WHERE `comment_book_id` = '$bookId'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    $_SESSION['messages'] = $messages['bookDeleted'];
    header('Location: ../index.php');
    exit;
}

function existSearchResults($connection, $bookTitle, $messages) {
    $sql = "SELECT `book_title`
            FROM `books`
            WHERE `book_title`
            LIKE '%$bookTitle%'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    $result = $query->num_rows;

    if ($result > 0) {
        return true;
    } else {
        return false;
    }
}

function getBookInfo($connection, $bookId, $messages) {
    $sql = 'SELECT
                b.book_id,
                b.book_title,
                a.author_id,
                a.author_name
            FROM `books` AS b

            LEFT JOIN `books_authors` AS ba
            ON b.book_id = ba.book_id

            LEFT JOIN `authors` AS a
            ON a.author_id = ba.author_id

            WHERE b.book_id = ?';

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $bookId);
    mysqli_stmt_execute($stmt);

    $rows = mysqli_stmt_result_metadata($stmt);

    while ($field = mysqli_fetch_field($rows)) {
        $fields[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $fields);

    $bookInfo = array();

    while (mysqli_stmt_fetch($stmt)) {
        $bookInfo['bookTitle'] = $row['book_title'];
        $bookInfo['author'][$row['author_id']] = $row['author_name'];
    }

    return $bookInfo;
}

function getAuthorName($connection, $authorId, $messages) {
    $sql = 'SELECT `author_name`
            FROM `authors`
            WHERE `author_id` = ?';

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $authorId);
    mysqli_stmt_execute($stmt);

    $rows = mysqli_stmt_result_metadata($stmt);

    while ($field = mysqli_fetch_field($rows)) {
        $fields[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $fields);
    mysqli_stmt_fetch($stmt);

    return $row['author_name'];
}

/**
 * Keep data for the logged user in the session
 * 
 * @param int $userId
 * @param string $username
 * @param int $accessLevel
 */
function keepDataForLoggedUser($userId, $username, $accessLevel = 1) {
    $_SESSION['isLogged'] = true;
    $_SESSION['userId'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['accessLevel'] = $accessLevel;
}

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
 * Print access level name of the current user
 * 
 * @param object $connection
 * @param int $accessLevel
 * @return string/void
 */
function printAccessLevelName($connection, $accessLevel) {
    $sql = "SELECT * FROM access_levels";
    
    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    while ($row = $query->fetch_assoc()) {
        if ($accessLevel == $row['access_lvl'] && $accessLevel != 1) {
            return '(' . $row['access_name'] . ')';
        }
    }
}

/**
 * Validate the username and if it's not valid
 * show user a message
 * 
 * @param string $username
 * @param array $messages
 * @return void
 */
function validateUsername($username, $messages) {
    if (mb_strlen($username) < 5 || mb_strlen($username) > 16) {
        $_SESSION['messages'] = $messages['usernameNotValidLength'];
        $_SESSION['temp-username'] = $username;
        header('Location: ../sign-up.php');
        exit;
    }

    if (!ctype_alnum(str_replace('_', '', $username))) {
        $_SESSION['messages'] = $messages['usernameNotValidContent'];
        $_SESSION['temp-username'] = $username;
        header('Location: ../sign-up.php');
        exit;
    }
}

/**
 * Validate the password and if it's not valid
 * show user a message
 * 
 * @param string $password
 * @param string $reenterPassword
 * @param array $messages
 * @return void
 */
function validatePassword($password, $reenterPassword, $messages) {
    if (mb_strlen($password) < 5 || mb_strlen($password) > 16 ||
            mb_strlen($reenterPassword) < 5 || mb_strlen($reenterPassword) > 16) {
        $_SESSION['messages'] = $messages['passwdNotValidLength'];
        header('Location: ../sign-up.php');
        exit;
    }

    if ($password !== $reenterPassword) {
        $_SESSION['messages'] = $messages['passwordsNotMatch'];
        header('Location: ../sign-up.php');
        exit;
    }
}

/**
 * Check if the username already exist
 * 
 * @param object $connection
 * @param string $username
 * @return boolean
 */
function usernameExist($connection, $username) {
    $sql = "SELECT * FROM `users`
            WHERE `name` = '" . $username . "'";

    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    if ($query->num_rows == 1) {
        return true;
    } else {
        return false;
    }
}

function getCommentsByBookId($connection, $bookId, $messages) {
    $sql = 'SELECT c.comment_id, c.comment_content, c.comment_user_id, c.comment_date, u.name
            FROM `comments` AS c            
            LEFT JOIN `users` AS u ON u.user_id = c.comment_user_id            
            WHERE `comment_book_id` = ?
            ORDER BY c.comment_date';

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $bookId);
    mysqli_stmt_execute($stmt);

    $rows = mysqli_stmt_result_metadata($stmt);

    while ($field = mysqli_fetch_field($rows)) {
        $fields[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $fields);

    $comments = array();

    while (mysqli_stmt_fetch($stmt)) {
        $comments[$row['comment_id']]['userId'] = $row['comment_user_id'];
        $comments[$row['comment_id']]['username'] = $row['name'];
        $comments[$row['comment_id']]['commentContent'] = $row['comment_content'];
        $comments[$row['comment_id']]['date'] = $row['comment_date'];
    }

    return $comments;
}

function getCommentsByUserId($connection, $userId, $messages) {
    $sql = 'SELECT
                c.comment_id,
                c.comment_book_id,
                c.comment_content,                
                c.comment_date,
                b.book_title
            FROM `comments` AS c
            LEFT JOIN `books` AS b ON c.comment_book_id = b.book_id
            WHERE `comment_user_id` = ?
            ORDER BY c.comment_date';

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);

    $rows = mysqli_stmt_result_metadata($stmt);

    while ($field = mysqli_fetch_field($rows)) {
        $fields[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $fields);

    $comments = array();

    while (mysqli_stmt_fetch($stmt)) {
        $comments[$row['comment_id']]['commentContent'] = $row['comment_content'];
        $comments[$row['comment_id']]['date'] = $row['comment_date'];
        $comments[$row['comment_id']]['bookId'] = $row['comment_book_id'];
        $comments[$row['comment_id']]['bookTitle'] = $row['book_title'];
    }

    return $comments;
}

/**
 * Check if the old password match
 * 
 * @param object $connection
 * @param string $oldPassword
 * @return boolean
 */
function oldPasswordMath($connection, $oldPassword) {
    $sql = "SELECT `passwd`
            FROM `users`
            WHERE `user_id` = '" . $_SESSION['userId'] . "'";

    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }
    
    $row = $query->fetch_assoc();

    if ($oldPassword === $row['passwd']) {
        return true;
    } else {
        return false;
    }
}

function getAllUsers($connection, $includeCurrentUser = true) {
    if ($includeCurrentUser) {
        $sql = "SELECT `user_id`, `name`, `access_lvl`
                FROM `users`";
    } else {
        $sql = "SELECT `user_id`, `name`, `access_lvl`
                FROM `users`
                WHERE `user_id` != '" . $_SESSION['userId'] . "'";
    }

    $query = mysqli_query($connection, $sql);

    while ($row = $query->fetch_assoc()) {
        $users['user_id'][] = $row['user_id'];
        $users['name'][] = $row['name'];
    }

    return $users;
}

function getUsernameById($connection, $id) {
    $id = mysqli_real_escape_string($connection, $id);
    
    $sql = "SELECT `name`
            FROM `users`
            WHERE `user_id` = '" . $id . "'";

    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }
    
    $row = $query->fetch_assoc();

    return $row['name'];
}

function getAllAccessLevels($connection) {
    $sql = "SELECT *
            FROM `access_levels`";

    $query = mysqli_query($connection, $sql);
    
     if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    while ($row = $query->fetch_assoc()) {
        $accessLevels['access_lvl'][] = $row['access_lvl'];
        $accessLevels['access_name'][] = $row['access_name'];
    }

    return $accessLevels;
}

function getAccessLevelByUserId($connection, $id) {
    $id = mysqli_real_escape_string($connection, $id);
    
    $sql = "SELECT `access_lvl`
            FROM `users`
            WHERE `user_id` = '" . $id . "'";

    $query = mysqli_query($connection, $sql);
    
     if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }
    
    $row = $query->fetch_assoc();

    return $row['access_lvl'];
}

function deleteUser($connection, $id) {
    $id = mysqli_real_escape_string($connection, $id);
    
    // Delete all comments written by this user
    
     $sql = "DELETE FROM `comments`
             WHERE `comment_user_id` = '" . $id . "'";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }
    
    // Delete the user
    
    $sql = "DELETE FROM `users`
            WHERE `user_id` = '" . $id . "'
            LIMIT 1";
    
    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }
}

function existSuchUserId($connection, $id) {
    $id = mysqli_real_escape_string($connection, $id);
    
    $sql = "SELECT `user_id`
            FROM `users`
            WHERE `user_id` = '" . $id . "'";

    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    if ($query->num_rows == 1) {
        return true;
    } else {
        return false;
    }
}

function existSuchAccessLevelId($connection, $accessLevelId) {
    $accessLevelId = mysqli_real_escape_string($connection, $accessLevelId);
    
    $sql = "SELECT `access_lvl`
            FROM `access_levels`
            WHERE `access_lvl` = '" . $accessLevelId . "'";

    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    if ($query->num_rows == 1) {
        return true;
    } else {
        return false;
    }
}

function changeAccessLevel($connection, $id, $accessLevelId, $messages) {
    $id = mysqli_real_escape_string($connection, $id);
    $accessLevelId = mysqli_real_escape_string($connection, $accessLevelId);
            
    $sql = "UPDATE `users`
            SET `access_lvl` = '" . $accessLevelId . "'
            WHERE `user_id` = '" . $id . "'";

    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }

    $_SESSION['messages'] = $messages['successfullUpdate'];
    header('Location: ../administration.php');
    exit;
}