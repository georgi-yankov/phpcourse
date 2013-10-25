<a href="index.php?page=authors">Автори</a>
<a href="index.php?page=add-book">Нова книга</a>

<table border="1"><tr><td>Книга</td><td>Автори</td></tr>
    <?php
    foreach ($result as $row) {
        echo '<tr><td>' . $row['book_title'] . '</td>
            <td>';
        $ar = array();
        foreach ($row['authors'] as $k => $va) {
            $ar[] = '<a href="index.php?author_id=' . $k . '">' . $va . '</a>';
        }
        echo implode(' , ', $ar) . '</td></tr>';
    };
    ?>
</table>