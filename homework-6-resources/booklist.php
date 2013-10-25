<?php

if (isset($_GET['author_id'])) {
    $author_id = (int) $_GET['author_id'];
    $filterByAuthorId = "WHERE authors.author_id = $author_id";
} else {
    $filterByAuthorId = '';
}

$q = mysqli_query($db, "SELECT books.book_id, books.book_title, authors.author_id, authors.author_name
    FROM books

    LEFT JOIN books_authors
    ON books.book_id = books_authors.book_id

    LEFT JOIN authors
    ON authors.author_id = books_authors.author_id

    WHERE books.book_title in (
        SELECT books.book_title
        FROM books
        LEFT JOIN books_authors ON books.book_id = books_authors.book_id
        LEFT JOIN authors ON authors.author_id = books_authors.author_id
        $filterByAuthorId
    )"
);

$result = array();
while ($row = mysqli_fetch_assoc($q)) {
    $result[$row['book_id']]['book_title'] = $row['book_title'];
    $result[$row['book_id']]['authors'][$row['author_id']] = $row['author_name'];
}

// Must pass in variables (as an array) to use in template  
$variables = array(
    'title'  => 'Списък',
    'result' => $result,
);

renderLayoutWithContentFile('booklist_public.php', $variables);