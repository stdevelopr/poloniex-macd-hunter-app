<!-- Drop MySQL Coins table -->
<?php 
ob_start();
include 'connect_db.php';
ob_end_clean();

$tb = 'DROP TABLE Coins';
if ($conn->query($tb) === TRUE) {
    echo "Table Coins deleted";
    echo '<br>';
} else {
    echo "Error..." . $conn->error;
    echo '<br>';
}