<?php
$pageTitle = 'Book Info';

require 'includes/header.php';

if (!isset($_GET['book']) || $_GET['book'] == '') {
    header('Location: index.php');
    exit;
}

$bookId = (int) $_GET['book'];

if (!bookExistById($connection, $bookId, $messages)) {
    $_SESSION['messages'] = $messages['bookNotExist'];
    header('Location: index.php');
    exit;
}

$bookInfo = getBookInfo($connection, $bookId, $messages);
?>

<h2>Book Info</h2>

<div id="book-title">
    <div class="book-info-label">Title:</div>
    <div class="book-info-content"><?php echo $bookInfo['bookTitle']; ?></div>
</div>

<div id="book-authors">
    <div class="book-info-label">Authors:</div>
    <div class="book-info-content">
        <?php
        foreach ($bookInfo['author'] as $key => $value) {
            // Store links in array so that afterwords
            // to avoid the last comma
            $authorsLinks[] = '<a href=index.php?author=' . $key . ' title="Books from ' . $value . '">' . $value . '</a>';
        }

        $authorsLinksResult = implode(', ', $authorsLinks);
        echo $authorsLinksResult;
        ?>
    </div><!-- .book-info-content -->
</div>

<?php if ($existLoggedUser) { ?>
    <div id="add-comment">
        <form method="POST" action="processing/manage-add.php" role="form">
            <p>
                <label for="comment-txt">Comment:</label>
                <textarea name="comment-txt" id="comment-txt" required><?php echo isset($_SESSION['tempComment']) ? trim($_SESSION['tempComment']) : '' ?></textarea>
            </p>
            <p>
                <label></label><!-- used only with presentation purpose -->
                <input type="hidden" name="book-id" value="<?php echo $bookId; ?>" />
                <input type="submit" name="add-comment" value="Add" />
            </p>
        </form>
    </div><!-- #add-comment -->
<?php } ?>

<div id="comments">
    <?php $comments = getCommentsByBookId($connection, $bookId, $messages); ?>
    <?php $countComments = count($comments); ?>
    <?php $counter = 0; ?>
    <h3>Comments (<?php echo $countComments; ?>)</h3>   
    
        <?php foreach ($comments as $key => $value) { ?>
            <div class="single-comment">
                <p>
                    <?php echo ++$counter; ?>. <a href="comments.php?user=<?php echo $value['userId']; ?>" title="Comments from <?php echo $value['username']; ?>"><?php echo $value['username']; ?></a>
                    | <?php echo date('d/m/Y | H:i', strtotime($value['date'])); ?>
                </p>
                <p><?php echo nl2br($value['commentContent']); ?></p>                
            </div><!-- .single-comment -->
        <?php } ?>
</div><!-- #comments -->

<?php
if (isset($_SESSION['tempComment'])) {
    unset($_SESSION['tempComment']);
}

require 'includes/footer.php';