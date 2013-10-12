<?php

$pageTitle = 'Search';

require 'includes/header.php';

if (!isset($_POST['search'])) {
    header('Location: index.php');
    exit;
}

if (trim($_POST['book-title']) == '') {
    $_SESSION['messages'] = $messages['emptySearchField'];
    header('Location: index.php');
    exit;
}

$bookTitle = safeInput($_POST['book-title']);
$bookTitle = mysqli_escape_string($connection, $bookTitle);
?>

<h2>Search results for <span>"<?php echo $bookTitle; ?>"</span></h2>

<?php
if (existSearchResults($connection, $bookTitle, $messages)) {
    $sql = "
        SELECT *
        FROM `books` AS bks

        LEFT JOIN `books_authors` AS bks_aut
        ON bks.book_id = bks_aut.book_id

        LEFT JOIN `authors` AS aut
        ON bks_aut.author_id = aut.author_id

        WHERE bks.book_title
        LIKE '%$bookTitle%'
        ORDER BY bks.book_title ASC
    ";

    $query = mysqli_query($connection, $sql);

    if (!$query) {
        $_SESSION['messages'] = $messages['wrongQueryExecution'];
        header('Location: index.php');
        exit;
    }

    while ($row = $query->fetch_assoc()) {
        $allInfo[$row['book_id']]['bookTitle'] = $row['book_title'];
        $allInfo[$row['book_id']]['authorInfo'][$row['author_id']] = $row['author_name'];
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>№</th>
                <th>Book</th>
                <th>Authors</th>
            </tr>
        </thead>

        <tbody>
            <?php $booksCounter = 0; ?>
            <?php foreach ($allInfo as $key => $value) { // $key is not used yet ?>
                <?php $booksCounter++; ?>
                <tr>
                    <td><?php echo $booksCounter; ?>.</td>
                    <td><?php echo $value['bookTitle'] ?></td>
                    <td>
                        <?php
                        foreach ($value['authorInfo'] as $kk => $vv) {
                            // Store links in array so that afterwords
                            // to avoid the last comma
                            $authorsLinks[] = '<a href=index.php?author=' . $kk . ' title="Books from ' . $vv . '">' . $vv . '</a>';
                        }

                        $authorsLinksResult = implode(',&nbsp;&nbsp;', $authorsLinks);
                        echo $authorsLinksResult;

                        unset($authorsLinks);
                        unset($authorsLinksResult);
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php } else { ?>
    <p>There was no any book matching your search.</p>
<?php } ?>

<?php
require 'includes/footer.php';