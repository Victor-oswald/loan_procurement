<?php
session_start();
date_default_timezone_set('Africa/Lagos');

$currentDateTime = date('Y-m-d H:i:s');
try {
    $pdo = new PDO("mysql:host=localhost;dbname=loan_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['Error: ' . $e->getMessage()]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user=$_SESSION['user'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM loan");
        $stmt->execute();
        $loanData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $loanData,
        ]);
    } catch (PDOException $e) {
        echo json_encode(['Error: ' . $e->getMessage()]);
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['user'];
    $loanReason = $_POST["loanReason"];
    $loanType = $_POST["loanType"];
    $schedule = $_POST['schedule'];
    $account = $_POST['account'];
    $bank = $_POST['bank'];
    $status='pending';


    if ($loanType == "student") {
        $studentID = $_POST["studentID"];
        $institution = $_POST["institution"];

        $stmt = $pdo->prepare("INSERT INTO loan (`user`, `loan_type`, `student_id`, `institution`, `Reason`, `schedule`,`loan_status`,`request_date`,`account`,`bank`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $loanType, $studentID, $institution, $loanReason, $schedule,$status,$currentDateTime,$account, $bank]);
        echo json_encode([
            'status' => 'success',
            'message' => 'Loan application submitted successfully',
            'data' => [
                'loanReason' => $loanReason,
                'loanType' => $loanType,
                'schedule' => $schedule,
                'Account Number' => $account,
                'Bank' => $bank,
                'studentID' => isset($studentID) ? $studentID : null,
                'institution' => isset($institution) ? $institution : null,
            ]
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO loan (`user`, `loan_type`, `Reason`, `schedule`,`loan_status`,`request_date`,`account`,`bank`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $loanType, $loanReason, $schedule,$status,$currentDateTime,$account, $bank]);
        echo json_encode([
            'status' => 'success',
            'message' => 'Loan application submitted successfully',
            'data' => [
                'loanReason' => $loanReason,
                'loanType' => $loanType,
                'schedule' => $schedule,
                'Account Number' => $account,
                'Bank' => $bank,
            ]
        ]);
    }
}
?>
