<?php

session_start();

require './includes/functions.php';

if (!existLoggedUser()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Gallery';

require './includes/header.php';
?>

<h2><?php echo $pageTitle; ?></h2>

<ul id="gallery">
    <li>
        <a href="upload.php">Add New Photo</a>
    </li>
    
    <?php
    $postsDataFile = 'data/posts.txt';
    $allPosts = file($postsDataFile);
    
    foreach ($allPosts as $value) {
        $singlePostArray = explode('|', $value);
        if ($userId == $singlePostArray[1]) {
            
            $postId = $singlePostArray[0];
            $fileSize = getReadableFileSize($singlePostArray[2]);
            $photoTitle = $singlePostArray[3];
            
            $largeImage = 'users' . DIRECTORY_SEPARATOR . 'user-' . $userId . DIRECTORY_SEPARATOR . $postId . '.jpg';
            $thumb = 'users/user-' . $userId . '/thumbs/' . $postId . '.jpg';     
            
            echo '<li>';
            echo '<a href="' . $largeImage . '">';
            echo '<img src="' . $thumb . '" alt="Photo" width="143" height="143" />';
            echo '</a>';
            echo '<p class="photo-title"><a class="overflow-safe" href="' . $largeImage . '" title="' . $photoTitle . '">' . $photoTitle . '</a></p>';
            echo '<p class="photo-details">';
            echo '<span class="download-icon"><a href="processing/download.php?post=' . $postId . '" title="download image"></a></span>';
            echo '<span class="file-size">' . $fileSize . '</span>';
            echo '<span class="action"><a class="edit-link" href="edit.php?post=' . $postId . '&title=' . urlencode($photoTitle) . '">Edit</a> | ';
            echo '<a class="delete-link" href="processing/action.php?post=' . $postId . '">Delete</a></span>';
            echo '</p>';
            echo '</li>';
        }
    }
    ?>
</ul>

<?php
require './includes/footer.php';