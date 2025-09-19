<?php
// Database config and connection logic
$host = 'localhost';
$db = 'my_api_db';
$user = 'root'; 
$pass = '';    

// Disable mysqli exceptions for error handling
mysqli_report(MYSQLI_REPORT_OFF);

// Connect to MySQL server (no DB yet)
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database server connection failed.']));
}

// Try to select the database, create if it doesn't exist
if (!$conn->select_db($db)) {
    $createDbSql = "CREATE DATABASE `$db`";
    if (!$conn->query($createDbSql)) {
        $conn->close();
        die(json_encode(['success' => false, 'message' => 'Failed to create database.']));
    }
    $conn->select_db($db);
}
?>
