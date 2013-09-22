<?php
$pageTitle = 'List';
include './includes/constants.php';
include './includes/header.php';
?>
            <div>
                <a href="expenses.php?action=add" class="button">Add new expense</a>
            </div>

            <?php
            if (isset($_GET['deleted']) && $_GET['deleted'] == true) {
                echo '<div id="success-message">';
                echo '<p>Item successfully deleted.</p>';
                echo '</div>';            
            }
            ?>

            <div id="filter">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                    <select name="date">
                        <?php
                            if (isset($_GET['date']) && !empty($_GET['date'])) {
                                $selectDate = $_GET['date'];
                            } else {
                                $selectDate = 'All';
                            }
                        
                            if (file_exists('expenses-data.txt')) {
                                $fileContent = file('expenses-data.txt');
                                $allDates = array();
                                
                                foreach ($fileContent as $value) {
                                    $columns = explode('!', $value);
                                    $allDates[] = $columns[1];
                                }
                                
                                $uniqueDates = array_unique($allDates);
                                array_unshift($uniqueDates, 'All');
                                
                                foreach ($uniqueDates as $value) {
                                    if ($value == $selectDate) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    echo '<option value="' . $value . '"' . $selected . '>' . $value . '</option>';
                                }                                
                            }
                        ?>
                    </select>

                    <select name="select-type">
                        <?php
                        if (isset($_GET['select-type'])) {
                            $selectType = $_GET['select-type'];
                        } else {
                            $selectType = '0';
                        }
                                              
                        foreach ($itemsType as $key => $value) {
                            if ($key == $selectType) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            
                            echo '<option value="' . $key.'"' . $selected . '>' . $value . '</option>';
                        }
                        ?>
                    </select>

                    <input type="submit" value="Filter" />
                </form>
            </div><!-- #filter -->

            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $existEnteredData = false;
                    $existFilterredData = false;
                    if (file_exists('expenses-data.txt')) {
                        $fileContent = file('expenses-data.txt');                        
                        
                        if (count($fileContent) == 0) {
                            echo '<tr><td colspan="6">';
                            echo 'Currently there are no any expenses entered!';
                            echo '</td></tr>';
                        } else {  
                            $existEnteredData = true;
                            $row = -1;
                            $lineNumber = 1;
                            $totalPrice = 0;                            
                            foreach ($fileContent as $value) {
                                $row++;
                                $columns = explode('!', $value);
                                
                                if (($selectType != 0) && (trim($columns[4]) != $selectType)) {
                                    continue;
                                }
                                
                                if (($selectDate != 'All') && ($selectDate != $columns[1])) {
                                    continue;
                                }
                                
                                echo '<tr>';
                                echo '<td>' . $lineNumber . '</td>';
                                echo '<td>' . $columns[1] . '</td>';
                                echo '<td>' . $columns[2] . '</td>';
                                echo '<td>' . $columns[3] . '</td>';
                                echo '<td>' . $itemsType[trim($columns[4])] . '</td>';
                                echo '<td>';
                                echo '<a href="expenses.php?action=edit&row=' . $row . '" class="edit-link">Edit</a> | ';
                                echo '<a href="action.php?deleteRow=' . $row . '" class="delete-link">Delete</a>';
                                echo '</td>';
                                echo '</tr>';
                                
                                $totalPrice += $columns[3];
                                $lineNumber++;
                                $existFilterredData = true;
                            }
                            
                            if (!$existFilterredData) {
                                echo '<tr><td colspan="5">';
                                echo 'Currently there are no any expenses for your filter!';
                                echo '</td></tr>';
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            
            <?php
            if ($existEnteredData && $existFilterredData) {
               echo '<p class="bold">Total price: ' . number_format($totalPrice, 2) . '</p>'; 
            }
            ?>            
<?php
include './includes/footer.php';
?>