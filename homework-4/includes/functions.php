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

function bookExist($connection, $bookTitle, $messages) {
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

function authorHasBooks($connection, $authorId, $messages) {
    $sql = "SELECT `author_id`
            FROM `books_authors`
            WHERE `author_id` = $authorId";
    
    $query = mysqli_query($connection, $sql);
    
    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: ../index.php');
        exit;
    }
    
    $result = $query->num_rows;

    if ($result > 0) {
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