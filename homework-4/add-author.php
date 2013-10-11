<?php
session_start();

$pageTitle = 'Add Author';

require 'includes/header.php';

if (isset($_SESSION['tempAuthorName'])) {
    $authorName = $_SESSION['tempAuthorName'];
} else {
    $authorName = '';
}

$allAuthorsData = getAllAuthors($connection, $messages);
?>

<h2>Add New Author</h2>

<div id="add-author-form">
    <form method="POST" action="processing/manage-add.php" role="form">
        <label for="author-name">Author: </label>           
        <input type="text" name="author-name" id="author-name" required autofocus
               value="<?php echo $authorName; ?>" />
        <input type="submit" name="add-author" value="Add" />
    </form>
</div><!-- #add-author-form -->

<?php if (!empty($allAuthorsData)) { ?>
<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Authors</th>
        </tr>
    </thead>

    <tbody>
        <?php for ($i = 0; $i < count($allAuthorsData['authorId']); $i++) { ?>
            <tr>
                <td><?php echo ($i + 1); ?>.</td>
                <td>
                    <a href="index.php?author=<?php echo $allAuthorsData['authorId'][$i]; ?>"
                       title="Books from <?php echo $allAuthorsData['authorName'][$i]; ?>">
                        <?php echo $allAuthorsData['authorName'][$i]; ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>

<?php
if (isset($_SESSION['tempAuthorName'])) {
    unset($_SESSION['tempAuthorName']);
}

require 'includes/footer.php';