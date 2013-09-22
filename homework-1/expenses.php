<?php
$pageTitle = 'Expenses';
include './includes/constants.php';
include './includes/header.php';
?>
            <div>
                <a href="index.php" class="button">List expenses</a>
            </div>

            <?php
            if (isset($_GET['successMessage']) && !empty($_GET['successMessage'])) {
                echo '<div id="success-message">';
                echo $_GET['successMessage'];
                echo '</div>';            
            } else if (isset($_GET['errorMessage']) && !empty($_GET['errorMessage'])) {
                echo '<div id="error-message">';
                echo $_GET['errorMessage'];
                echo '</div>';            
            }
            
            if (isset($_GET['action']) && $_GET['action'] == 'edit' &&
                isset($_GET['row'])) {                
                $fileContent = file('expenses-data.txt');
                $itemData = $fileContent[$_GET['row']];
                $columns = explode('!', $itemData);
                
                $date = $columns[1];
                $item = $columns[2];
                $price = $columns[3];
                $type = $columns[4];
            } else {
                $date = date("d.m.Y");
                $item = '';
                $price = '';
                $type = '';
            }
            ?>

            

            <form id="expenses-form" action="action.php" method="post" role="form">
                <p>
                    <label for="tb-date">Date:</label>
                    <input id="tb-date" name="tb-date" type="text" value="<?php echo $date; ?>" required="" tabindex="1" />
                </p>
                <p>
                    <label for="tb-item">Item:</label>
                    <input id="tb-item" name="tb-item" type="text" value="<?php echo $item; ?>" required="" tabindex="2" />
                </p>
                <p>
                    <label for="tb-price">Price:</label>
                    <input id="tb-price" name="tb-price" type="text" value="<?php echo $price; ?>" required="" tabindex="3" />
                </p>
                <p>
                    <label for="select-type">Type:</label>                    
                    <select id="select-type" name="select-type" tabindex="4">
                        <?php
                        foreach ($itemsType as $key => $value) {
                            if ($key == 0) {
                                continue;
                            }
                            
                            if ($type == $key) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            
                            echo '<option value="' . $key.'" ' . $selected . '>' . $value . '</option>';
                        }
                        ?>
                    </select>
                </p>                
                
                <p>
                    <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'add') {
                        echo '<input type="submit" name="action" value="Add" tabindex="5">';
                    } else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
                        echo '<input type="submit" name="action" value="Edit" tabindex="5">';
                        echo '<input type="hidden" name="row" value="' . $_GET['row'] . '" />';
                    }
                    ?>                    
                </p>
            </form>
<?php
include './includes/footer.php';
?>