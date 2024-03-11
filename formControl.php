<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "loan_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    $type = $_POST['type'];
    $amount = $_POST['amount'];

    if ($type == 'confirm') {

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch the current value of repayed_amount
        $fetchStmt = $conn->prepare("SELECT repayed_amount FROM loan WHERE user = ?");
        $fetchStmt->bind_param("s", $_SESSION['user']);
        $fetchStmt->execute();
        $fetchStmt->bind_result($repayedAmount);
        $fetchStmt->fetch();
        $fetchStmt->close();

        // Calculate the new total
        $newTotal = $repayedAmount + $amount;

        // Update the repayed_amount with the new total
        $updateStmt = $conn->prepare("UPDATE loan SET repayed_amount = ? WHERE user = ?");
        $updateStmt->bind_param("ss", $newTotal, $_SESSION['user']);

        if ($updateStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Loan data updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating data: ' . $conn->error]);
        }

        $updateStmt->close();
        $conn->close();
    } elseif ($type == 'update') {
        $approve_amount = $_POST['amount'];
        $user = $_POST['user'];

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Ensure to use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE active_loan SET approved_repayed_amount = ? WHERE user = ?");
        $stmt->bind_param("ss", $approve_amount, $user);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Loan data updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating data: ' . $conn->error]);
        }

        $stmt->close();
        $conn->close();
        } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid "type" value.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid access to the script.']);
}
?>
