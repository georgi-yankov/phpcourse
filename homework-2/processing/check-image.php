<?php

session_start();

require '../includes/functions.php';
require '../includes/messages.php';

// If a form is not submitted forward the
// user to the index page
if (!isset($_POST['upload'])) {
    header('Location: ../index.php');
    exit();
}

$photoTitle = safeInput($_POST['photo-title']);
$fileTempname = $_FILES['file']['name'];
$fileSize = $_FILES['file']['size'];

if (mb_strlen($photoTitle) > 40) {
    $_SESSION['messages'] = $messages[12];
    header('Location: ../upload.php');
    exit();
}

if ($photoTitle == '') {
    $photoTitle = 'no title';
}

if ($fileTempname == '') {
    $_SESSION['messages'] = $messages[10];
    header('Location: ../upload.php');
    exit();
}

$userId = $_SESSION['userId'];

$path = '../users/user-' . $userId;
$imageDir = realpath($path);
$imageThumb = $imageDir . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR;

$imageName = $imageDir . DIRECTORY_SEPARATOR . $fileTempname;

if (move_uploaded_file($_FILES['file']['tmp_name'], $imageName)) {
    // Check if the file being uploaded is not an image
    if (!(@getimagesize($imageName))) {
        unlink($imageName);
        $_SESSION['messages'] = $messages[11];
        header('Location: ../upload.php');
        exit();
    }

    // Get info about the image being uploaded
    list($width, $height, $type, $attr) = getimagesize($imageName);

    // The file is an image
    // Now check if it is an acceptable type format
    if ($type > 3) {
        unlink($imageName);
        $_SESSION['messages'] = $messages[11];
        header('Location: ../upload.php');
        exit();
    }

    // Restrict the user to upload files up to 2MB
    $maxSize = 2 * 1024 * 1024; // convert to bytes
    if ($fileSize > $maxSize) {
        unlink($imageName);
        $_SESSION['messages'] = $messages[14];
        header('Location: ../upload.php');
        exit();
    }

    // Insert new post  
    $postsDataFile = '../data/posts.txt';
    if (file_exists($postsDataFile)) {
        $allPosts = file($postsDataFile);
        $postId = getNextId($allPosts);
        $dataToInsert = $postId . '|' . $userId . '|' . $fileSize . '|' . $photoTitle . "\n";
        file_put_contents($postsDataFile, $dataToInsert, FILE_APPEND);

        $newImageName = $imageDir . DIRECTORY_SEPARATOR . $postId . '.jpg';

        if ($type == 2) {
            rename($imageName, $newImageName);
        } else {
            if ($type == 1) {
                $imageNameOld = imagecreatefromgif($imageName);
                unlink($imageName);
            } elseif ($type == 3) {
                $imageNameOld = imagecreatefrompng($imageName);
                unlink($imageName);
            }

            // Convert the image to jpg
            $imageJpg = imagecreatetruecolor($width, $height);
            imagecopyresampled($imageJpg, $imageNameOld, 0, 0, 0, 0, $width, $height, $width, $height);
            imagejpeg($imageJpg, $newImageName);
            imagedestroy($imageNameOld);
            imagedestroy($imageJpg);
            
            
        }

        $newThumbName = $imageThumb . $postId . '.jpg';

        // Dimensions for the thumbnail
        $thumbWidth = 143;
        $thumbHeight = 143;

        // Create the thumbnail
        $largeImage = imagecreatefromjpeg($newImageName);
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        // If the image is not a square we need to create a non smashed thumbnail
        if ($height >= $width) {
            imagecopyresampled($thumb, $largeImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $width);
        } else {
            imagecopyresampled($thumb, $largeImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $height, $height);
        }

        imagejpeg($thumb, $newThumbName);
        imagedestroy($largeImage);
        imagedestroy($thumb);

        $_SESSION['messages'] = $messages[13];
        header('Location: ../upload.php');
        exit();
    }
}