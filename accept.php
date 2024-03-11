<?php
session_start();
date_default_timezone_set('Africa/Lagos');

$currentDateTime = date('Y-m-d');
$user=$_SESSION['user'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loan_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$input_data = file_get_contents('php://input');

$data = json_decode($input_data, true);

$interest = $conn->real_escape_string($data['interest']); 
$amount = $conn->real_escape_string($data['amount']); 
$schedule = $conn->real_escape_string($data['schedule']); 


$sql = "INSERT INTO active_loan (user, loan_amount, interest,schedule, date) VALUES ('$user', '$amount','$interest','$schedule','$currentDateTime')";

if ($conn->query($sql) === TRUE) {
    $response = [
        'status' => 'success',
        'message' => 'Loan accepted and saved to the database successfully',
        'data' => $data,
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Error saving loan to the database: ' . $conn->error,
    ];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);

?>
