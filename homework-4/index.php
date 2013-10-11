<?php
session_start();

$pageTitle = 'Books';

require 'includes/header.php';
?>

<h2>All Books</h2>

<table>
    <thead>
        <tr>
            <th>№</th>
            <th>
                <a href="" title="">Book</a>
            </th>
            <th>Authors</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>1.</td>
            <td>book 1</td>
            <td><a href="" title="">author 1</a></td>
        </tr>
        <tr>
            <td>2.</td>
            <td>book 2</td>
            <td><a href="" title="">author 2</a></td>
        </tr>
    </tbody>
</table>

<?php
require 'includes/footer.php';