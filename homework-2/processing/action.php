<?php

session_start();

require '../includes/functions.php';
require '../includes/messages.php';

if (!existLoggedUser()) {
    header('Location: ../index.php');
    exit();
}

$postsDataFile = '../data/posts.txt';
$allPosts = file($postsDataFile);

// Edit data
if (isset($_POST['edit']) && $_POST['edit'] == 'Edit') {
    $postId = $_POST['post'];
    $photoTitle = safeInput($_POST['photo-title']);
    if ($photoTitle == '') {
        $photoTitle = 'no title';
    }
    
    // Validate photo title
    if (mb_strlen($photoTitle) > 40) {
        $_SESSION['messages'] = $messages[12];
        header('Location: ../edit.php?post=' . $postId . '&title=' . urlencode($photoTitle));
        exit();
    }
    

    foreach ($allPosts as $value) {
        $singlePostArray = explode('|', $value);
        if ($postId == $singlePostArray[0]) {
            $dataToDelete = $value;
            $dataToAdd = $singlePostArray[0] . '|' . $singlePostArray[1] . '|' .
                    $singlePostArray[2] . '|' . $photoTitle . "\n";

            $newFileContent = str_replace($dataToDelete, $dataToAdd, file_get_contents($postsDataFile));
            file_put_contents($postsDataFile, $newFileContent);

            $_SESSION['messages'] = $messages[16];
            header('Location: ../edit.php?post=' . $postId . '&title=' . urlencode($photoTitle));
            exit();
        }
    }
}

// Delete data
if (isset($_GET['post'])) {
    $postId = (int) $_GET['post'];

    foreach ($allPosts as $value) {
        $singlePostArray = explode('|', $value);
        if ($postId == $singlePostArray[0]) {
            // Delete post from the file
            $dataToDelete = $value;
            $newFileContent = str_replace($dataToDelete, '', file_get_contents($postsDataFile));
            file_put_contents($postsDataFile, $newFileContent);

            // Remove post images from the directories
            $largeImage = '../users' . DIRECTORY_SEPARATOR . 'user-' . $_SESSION['userId'] . DIRECTORY_SEPARATOR . $postId . '.jpg';
            $thumb = '../users/user-' . $_SESSION['userId'] . '/thumbs/' . $postId . '.jpg';
            unlink($largeImage);
            unlink($thumb);

            $_SESSION['messages'] = $messages[15];
            header('Location: ../gallery.php');
            exit();
        }
    }
}