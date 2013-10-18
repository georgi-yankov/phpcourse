<?php

$pageTitle = 'Books';

require 'includes/header.php';

if (isset($_GET['author']) && $_GET['author'] != 'all-authors') {
    $authorId = (int) $_GET['author'];
    $authorId = mysqli_real_escape_string($connection, $authorId);
    
    if (!authorExistById($connection, $authorId, $messages)) {
        $_SESSION['messages'] = $messages['authorNotExist'];
        header('Location: index.php');
        exit;
    }
    
    if (!authorHasBooks($connection, $authorId, $messages)) {
        $_SESSION['messages'] = $messages['authorHasNotBooks'];
        header('Location: index.php');
        exit;
    }
    
    $filterByAuthorId = "WHERE authors.author_id = $authorId";
} else {
    $filterByAuthorId = '';
}

if (isset($_GET['sort']) && $_GET['sort'] == 'DESC') {
    $currentSort = 'DESC';
    $nextSort = 'ASC';
} else {
    $currentSort = 'ASC';
    $nextSort = 'DESC';
}

$sql = "
    SELECT books.book_id, books.book_title, authors.author_id, authors.author_name
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
    )
        
    ORDER BY books.book_title $currentSort
";

$query = mysqli_query($connection, $sql);

if (!$query) {
    $_SESSION['messages'] = $messages['wrongQueryExecution'];
    header('Location: index.php');
    exit;
}

$allInfo = array();

while ($row = $query->fetch_assoc()) {
    $allInfo[$row['book_id']]['bookTitle'] = $row['book_title'];    
    $allInfo[$row['book_id']]['authorInfo'][$row['author_id']] = $row['author_name'];
}

if (isset($authorId)) {
    echo '<h2>Books from "' . getAuthorName($connection, $authorId, $messages) . '"</h2>';
} else {
    echo '<h2>All Books</h2>';
}
?>

<?php if (!empty($allInfo)) { ?>
    <div id="filter">
        <form method="GET" action="">
            <select name="author">
                <option value="all-authors">All Authors</option>
                
                <?php $allAuthorsData = getAllAuthors($connection, $messages); ?>
                
                <?php for ($i = 0; $i < count($allAuthorsData['authorId']); $i++) { ?>
                <?php
                    if (isset($authorId)) {                        
                        if ($allAuthorsData['authorId'][$i] == $authorId) {                            
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                    } else {
                        $selected = '';
                    }
                ?>                
                    <option value="<?php echo $allAuthorsData['authorId'][$i]; ?>" <?php echo $selected; ?>>
                        <?php echo $allAuthorsData['authorName'][$i]; ?>
                    </option>
                <?php } ?>
            </select>
            
            <input type="submit" value="Filter" />
        </form>
    </div><!-- #filter -->

    <table>
        <thead>
            <tr>
                <th>№</th>
                <th>
                    <?php
                    if (isset($authorId)) {
                        $authorIdUrl = '&author=' . $authorId;
                    } else {
                        $authorIdUrl = '';
                    }
                    ?>
                    <a href="index.php?sort=<?php echo $nextSort, $authorIdUrl; ?>"
                       title="Sort books in <?php echo $nextSort; ?> order">
                        Book
                    </a>
                </th>
                <th>Authors</th>
                <?php if(isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] > 1) { ?>
                    <th>Actions</th>
                <?php } ?>
            </tr>
        </thead>

        <tbody>
            <?php $booksCounter = 0; ?>
            <?php foreach ($allInfo as $key => $value) { ?>
                <?php $booksCounter++; ?>
                <tr>
                    <td><?php echo $booksCounter; ?>.</td>
                    <td>
                        <a href="book.php?book=<?php echo $key; ?>" title="More info about &quot;<?php echo $value['bookTitle']; ?>&quot;"><?php echo $value['bookTitle']; ?></a>
                        <span>[<?php echo count(getCommentsByBookId($connection, $key, $messages)); ?>]</span>
                    </td>
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
                    <?php if(isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] > 1) { ?>
                        <td>
                            <a class="delete" href="processing/manage-delete.php?book=<?php echo $key; ?>" title="Delete book" onclick="return confirm('Are you sure you want to delete this book?');">delete</a>
                        </td>
                    <?php } ?>
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