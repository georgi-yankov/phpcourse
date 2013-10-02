<?php

session_start();

require './includes/functions.php';

if (!existLoggedUser()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Upload';

require './includes/header.php';
?>

<h2><?php echo $pageTitle; ?></h2>

<div id="back-btn">
    <a href="gallery.php">Back to gallery</a>
</div>

<div id="upload-form">
    <form method="POST" action="processing/check-image.php" enctype="multipart/form-data" role="form">       
        <p>
            <label for="photo-title">Title:</label>
            <input id="photo-title" type="text" name="photo-title" />
        </p>
        <p>
            <label for="file">Upload:</label>
            <input id="file" type="file" name="file" />
        </p>
        <p>
            <input type="submit" name="upload" value="Upload" />            
            <input type="reset" name="reset" value="Clear" />
        </p>        
    </form>
</div><!-- #upload-form -->

<?php
require './includes/footer.php';