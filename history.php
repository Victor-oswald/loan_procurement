<?php
// history.php

session_start(); // Make sure to start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loan_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use proper escaping to prevent SQL injection
$user = $conn->real_escape_string($_SESSION['user']);

$query = "SELECT * FROM loan WHERE user='$user'";
$result = $conn->query($query);

if ($result) {
    $loanHistory = [];

    while ($row = $result->fetch_assoc()) {
        $loanHistory[] = $row;
    }

    echo json_encode($loanHistory);
} else {
    echo json_encode(['error' => 'Error fetching loan history: ' . $conn->error]);
}

$conn->close();
?>
