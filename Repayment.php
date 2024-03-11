<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=loan_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM active_loan WHERE user = ?");
    $stmt_check->execute([$_SESSION['user']]);
    $row_count = $stmt_check->fetchColumn();

    if ($row_count > 0) {
        $stmt = $pdo->prepare("SELECT * FROM active_loan WHERE user = ?");
        $stmt->execute([$_SESSION['user']]);
        $loanDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($loanDetails);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'No active loans for this user']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection error']);
    exit();
}
?>
