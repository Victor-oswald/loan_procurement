<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents("php://input");

    $data = json_decode($json_data, true);

    if ($data === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data.']);
        exit;
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "loan_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $user = $data['user'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $updateStmt = $conn->prepare("UPDATE loan SET loan_status = 'Settled' WHERE user = ? AND loan_status=? ORDER BY request_date DESC LIMIT 1");
    $status='approved';
    $updateStmt->bind_param("ss", $user,$status);

    if ($updateStmt->execute()) {
        $deleteStmt = $conn->prepare("DELETE FROM active_loan WHERE user = ? ");
        $deleteStmt->bind_param("s", $user);

        if ($deleteStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Loan request deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting loan request: ' . $conn->error]);
        }

        $deleteStmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating loan status: ' . $conn->error]);
    }

    $updateStmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid access to the script.']);
}
?>
