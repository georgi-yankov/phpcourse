<?php

session_start();

require './includes/functions.php';

if (!existLoggedUser()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Edit';

require './includes/header.php';

if (isset($_SESSION['messages'])) {
    echo $_SESSION['messages'];
    unset($_SESSION['messages']);
}

if (isset($_GET['post']) && isset($_GET['title'])) {
    $postId = (int) $_GET['post'];
    $photoTitle = $_GET['title'];
}
?>

<h2><?php echo $pageTitle; ?></h2>

<div id="back-btn">
    <a href="gallery.php">Back to gallery</a>
</div>

<div id="edit-form">
    <form method="POST" action="processing/action.php" role="form">       
        <p>
            <label for="photo-title">Title:</label>
            <input id="photo-title" type="text" name="photo-title" value="<?php echo $photoTitle; ?>" />
        </p>
        <p>
            <input type="hidden" name="post" value="<?php echo $postId; ?>" />
            <input type="submit" name="edit" value="Edit" />
        </p>        
    </form>
</div><!-- #edit-form -->

<?php
require './includes/footer.php';