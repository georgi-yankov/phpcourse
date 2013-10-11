<?php
session_start();

$pageTitle = 'Books';

require 'includes/header.php';

$sql = "
    SELECT *
    FROM `books` AS bks
    
    LEFT JOIN `books_authors` AS bks_aut
    ON bks.book_id = bks_aut.book_id
    
    LEFT JOIN `authors` AS aut
    ON bks_aut.author_id = aut.author_id
";

$query = mysqli_query($connection, $sql);

if (!$query) {
    $_SESSION['messages'] = $messages['wrongQueryExecution'];
    header('Location: ../index.php');
    exit;
}

$allInfo = array();

while ($row = $query->fetch_assoc()) {
    $allInfo[$row['book_id']]['bookTitle'] = $row['book_title'];    
    $allInfo[$row['book_id']]['authorInfo'][$row['author_id']] = $row['author_name'];
}

//echo '<pre>'.print_r($allInfo, true).'</pre>';
//die();
?>

<h2>All Books</h2>

<?php if (!empty($allInfo)) { ?>
    <table>
        <thead>
            <tr>
                <th>№</th>
                <th>
                    <a href="" title="">Book</a>
                </th>
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
                            $authorsLinks[] = '<a href=index.php?author="' . $kk . '" title="Books from ' . $vv . '">' . $vv . '</a>';
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
    <div id="no-books">
        <p>Currently there are no books in the catalog.</p>
        <p>Be the one to add first book <a title="Add Book" href="add-book.php">here</a>.</p>
    </div>
<?php } ?>

<?php
require 'includes/footer.php';