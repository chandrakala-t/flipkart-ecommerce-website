<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "flipkart_db";
$port = 3307;

// Establish database connection
$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database,
    $port
);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>