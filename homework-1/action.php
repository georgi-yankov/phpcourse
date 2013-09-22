<?php

mb_internal_encoding('UTF-8'); 

include './includes/constants.php';
include './includes/functions.php';

$fileName = 'expenses-data.txt';
$fileContentArray = file($fileName);
$action = $_POST['action'];

if (isset($_POST['row']) && $_POST['row'] != '') {
    $row = $_POST['row']; 
}

// Delete row
if (isset($_GET['deleteRow'])) {
    $deleteRow = $_GET['deleteRow'];
    $dataToDelete = $fileContentArray[$deleteRow];

    $fileContent = str_replace($dataToDelete, '', file_get_contents($fileName));
    file_put_contents($fileName, $fileContent);

    header('Location:index.php?deleted=true');
    exit();
}

$date = $_POST['tb-date'];
$date = safeInput($date);

$item = $_POST['tb-item'];
$item = safeInput($item);

$price = $_POST['tb-price'];
$price = safeInput($price);
$price = str_replace(',', '.', $price);
$price = (float) $price; 
$price = number_format($price, 2);

$selectType = safeInput($selectType);
$selectType = (int) $_POST['select-type'];

$existError = false;

$errorMessage = '';

if ((mb_strlen($item) <= 3) || (mb_strlen($item) > 20)) {
    $existError = true;
    $errorMessage .= '<p>The item must have from 4 to 20 symbols!</p>';
}

if (($price <= 0) || ($price > 1000000)) {
    $existError = true;
    $errorMessage .= '<p>The price must be from 0.1 to 1000000!</p>';
}

$dateParts = explode('.', $date);
$day = (int) $dateParts[0];
$month = (int) $dateParts[1];
$year = (int) $dateParts[2];
$isValidDate = checkdate($month, $day, $year);

if ((count($dateParts) != 3) || !$isValidDate) {
    $existError = true;
    $errorMessage .= '<p>Date must be in format dd.mm.yyyy!</p>';
}

if (!array_key_exists($selectType, $itemsType)) {
    $existError = true;
    $errorMessage .= '<p>Wrong item type!</p>';
}

if ($existError) {
    if ($row != '') {
       header('Location:expenses.php?action=' . strtolower($action) . '&errorMessage=' . $errorMessage . '&row=' . $row); 
    } else {
       header('Location:expenses.php?action=' . strtolower($action) . '&errorMessage=' . $errorMessage);
    }
    
    exit();
}

$isActionDone = false;

switch ($action) {
    case 'Add':
        if (count($fileContentArray) == 0) {
            $id = 1;
        } else {
            $lastRow = $fileContentArray[count($fileContentArray) - 1];
            $lastRowArray = explode('!', $lastRow);
            $id = $lastRowArray[0] + 1;
        }
        
        $dataToAdd = $id . '!' . $date . '!' . $item . '!' . $price . '!' . $selectType . "\n";
        file_put_contents($fileName, $dataToAdd, FILE_APPEND);
        $isActionDone = true;
        break;
    case 'Edit':
        $dataToReplace = $fileContentArray[$row];
        $dataToReplaceArray = explode('!', $dataToReplace);
        $id = $dataToReplaceArray[0];
        
        $dataToAdd = $id . '!' . $date . '!' . $item . '!' . $price . '!' . $selectType . "\n";             
        
        $fileContent = str_replace($dataToReplace, $dataToAdd, file_get_contents($fileName));
        file_put_contents($fileName, $fileContent);

        $isActionDone = true;
        break;
    default:
        break;
}

if ($isActionDone) {
    $successMessage = '<p>' . $action . 'ed successfully!</p>';
    
    if ($action == 'Edit') {
        header('Location:expenses.php?action=' . strtolower($action) . '&successMessage=' . $successMessage . '&row=' . $row);
        exit();
    } else {
        header('Location:expenses.php?action=' . strtolower($action) . '&successMessage=' . $successMessage);
        exit();
    }
    
} else {
    header("Location:expenses.php?errorMessage=<p>Unknown error!</p>");
    exit();
}