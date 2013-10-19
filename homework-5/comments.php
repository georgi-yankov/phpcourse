<?php
$pageTitle = 'User Info';

require 'includes/header.php';

if (!isset($_GET['user']) || $_GET['user'] == '') {
    header('Location: index.php');
    exit;
}

$userId = (int) $_GET['user'];

if (!existSuchUserId($connection, $userId)) {
    $_SESSION['messages'] = $messages['noSuchUserOrAccessLevel'];
    header('Location: index.php');
    exit;
}

$comments = getCommentsByUserId($connection, $userId, $messages);
$usernameCommented = getUsernameById($connection, $userId);
?>

<h2>Comments from <?php echo $usernameCommented; ?></h2>

<div id="comments">    
    <?php $countComments = count($comments); ?>
    <?php $counter = 0; ?>
    <h3>Comments (<?php echo $countComments; ?>)</h3>   
    
        <?php foreach ($comments as $key => $value) { ?>
            <div class="single-comment">
                <p>
                    <?php echo ++$counter; ?>. <a href="comments.php?user=<?php echo $userId; ?>" title="Comments from <?php echo $usernameCommented; ?>"><?php echo $usernameCommented; ?></a>
                    | <?php echo date('d/m/Y | H:i', strtotime($value['date'])); ?>
                </p>
                <p><?php echo nl2br($value['commentContent']); ?></p>
                <p>on book: <a href="book.php?book=<?php echo $value['bookId']; ?>" title="More info about &quot;<?php echo $value['bookTitle']; ?>&quot;"><?php echo $value['bookTitle']; ?></a></p>                
            </div><!-- .single-comment -->
        <?php } ?>
</div><!-- #comments -->

<?php
if (isset($_SESSION['tempComment'])) {
    unset($_SESSION['tempComment']);
}

require 'includes/footer.php';