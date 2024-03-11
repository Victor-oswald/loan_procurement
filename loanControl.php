<?php
date_default_timezone_set('Africa/Lagos');

$currentDateTime = date('Y-m-d H:i:s');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=loan_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection error']);
        exit();
    }

    $user = $_POST["user"];
    $loanAmount = $_POST["loanAmount"];
    $interestRate = $_POST["interestRate"];


    try {
        $stmt = $pdo->prepare("UPDATE loan SET loan_status = 'approved', approved_amount = ?, interest = ?, approved_date = ? WHERE user = ? AND loan_status=? ORDER BY request_date DESC LIMIT 1");
        $stmt->execute([$loanAmount, $interestRate, $currentDateTime, $user,'pending']);
        
        echo json_encode(['status' => 'success', 'message' => 'Loan approved successfully']);
    }catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating loan details: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
