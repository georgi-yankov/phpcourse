<a href="index.php">Списък</a>
<a href="index.php?page=add-book">Нова книга</a>
<form method="post" action="">
    Име: <input type="text" name="author_name" />
    <input type="submit" value="Добави" />    
</form>

<table border='1'>
    <tr><th>Автор</th></tr>

    <?php
    foreach ($authors as $row) {
        echo '<tr><td>' . $row['author_name'] . '</td></tr>';
    }
    ?>

</table>