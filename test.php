<?php
phpinfo();

echo '<hr>';
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'database_arsip';

$conn = @new mysqli($host, $user, $password, $db, 3306);
if ($conn->connect_error) {
    echo '<h3>MySQL Connection Failed</h3>';
    echo $conn->connect_error;
} else {
    echo '<h3>MySQL Connection Success</h3>';
    echo 'Connected to database: ' . $db;
    $conn->close();
}
