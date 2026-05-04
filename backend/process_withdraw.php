<?php
session_start();
require_once "sqli.php";

if (!isset($_SESSION['user_id'])) { header("Location: ../login.html"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $accNum = mysqli_real_escape_string($conn, $_POST['account_number']);
    $routing = mysqli_real_escape_string($conn, $_POST['routing_number']);

    // 1. Validate routing number against login_code
    $validateRouting = $conn->prepare("SELECT login_code FROM users WHERE id = ?");
    $validateRouting->bind_param("i", $userId);
    $validateRouting->execute();
    $routingRes = $validateRouting->get_result()->fetch_assoc();
    
    if (empty($routing) || $routing != $routingRes['login_code']) {
        header("Location: ../withdraw.php?error=invalid_routing");
        exit();
    }

    // 2. Check current balance first
    $check = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $check->bind_param("i", $userId);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if ($res['balance'] < $amount) {
        header("Location: ../withdraw.php?error=insufficient_funds");
        exit();
    }

    // 2. Process Withdrawal
    $conn->autocommit(false);
    try {
        // Record in history
        $stmt1 = $conn->prepare("INSERT INTO withdrawals (user_id, amount, account_number, routing_number) VALUES (?, ?, ?, ?)");
        $stmt1->bind_param("idss", $userId, $amount, $accNum, $routing);
        $stmt1->execute();

        // Subtract from original table
        $stmt2 = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt2->bind_param("di", $amount, $userId);
        $stmt2->execute();

        $conn->commit();
        $conn->autocommit(true);
        header("Location: ../withdraw_success.php");
    } catch (Exception $e) {
        $conn->rollback();
        $conn->autocommit(true);
        header("Location: ../withdraw.php?status=error");
    }
}

$bankName = mysqli_real_escape_string($conn, $_POST['Bank_Name']);
$swiftCode = mysqli_real_escape_string($conn, $_POST['Swift_code']);

// Record in history
$stmt1 = $conn->prepare("INSERT INTO withdrawals (user_id, amount, account_number, routing_number, bank_name, swift_code) VALUES (?, ?, ?, ?, ?, ?)");
$stmt1->bind_param("idsss", $userId, $amount, $accNum, $routing, $bankName, $swiftCode);
$stmt1->execute();
?>