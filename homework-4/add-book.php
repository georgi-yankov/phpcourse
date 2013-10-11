<?php
session_start();

$pageTitle = 'Add Book';

require 'includes/header.php';

if (isset($_SESSION['tempBookTitle'])) {
    $bookTitle = $_SESSION['tempBookTitle'];
} else {
    $bookTitle = '';
}

if (isset($_SESSION['tempAuthors'])) {
    $authors = $_SESSION['tempAuthors'];
}

$allAuthorsData = getAllAuthors($connection, $messages);
?>

<h2>Add New Book</h2>

<div id="add-book-form">
    <form method="POST" action="processing/manage-add.php" role="form">
        <p>
            <label for="book-title">Book Title: </label>           
            <input type="text" name="book-title" id="book-title" required autofocus
                   autocomplete="off" value="<?php echo $bookTitle; ?>" />
        </p>
        <p>
            <label>Choose Authors: </label>
            <select name="authors[]" multiple required>
                <?php for ($i = 0; $i < count($allAuthorsData['authorId']); $i++) { ?>
                <?php
                    if (isset($_SESSION['tempAuthors'])) {
                        if (in_array($allAuthorsData['authorId'][$i], $authors)) {
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
        </p>
        <p>
            <label></label> <!-- used only for presentation purpose -->
            <input type="submit" name="add-book" value="Add" />
            <input type="reset" />
        </p>
    </form>
</div><!-- #add-book-form -->

<?php
if (isset($_SESSION['tempBookTitle'])) {
    unset($_SESSION['tempBookTitle']);
}

if (isset($_SESSION['tempAuthors'])) {
    unset($_SESSION['tempAuthors']);
}

require 'includes/footer.php';