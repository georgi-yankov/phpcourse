<?php

// Local
define('SQL_HOST', 'XXXXXXXX');
define('SQL_USER', 'XXXXXXXX');
define('SQL_PASS', 'XXXXXXXX');
define('SQL_DB', 'XXXXXXXX');

$connection = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);

if (!$connection) {
    echo 'No database connection.';
    exit;
}

mysqli_set_charset($connection, 'utf8');