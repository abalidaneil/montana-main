<?php
session_start();
if (!isset($_SESSION['admin_id'])) { die("Access Denied"); }
require_once "sqli.php";

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['user_id']) ? $_POST['user_id'] : '');
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($action == 'verify') {
    // Update User Verification Status
    $stmt = $conn->prepare("UPDATE users SET verify_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
} 

if ($action == 'withdraw') {
    // Update Withdrawal Request Status
    $stmt = $conn->prepare("UPDATE withdrawals SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

if ($action == 'update_currency') {
    // Update User Currency
    $currency = isset($_POST['currency']) ? $_POST['currency'] : 'USD';
    $stmt = $conn->prepare("UPDATE users SET currency = ? WHERE id = ?");
    $stmt->bind_param("si", $currency, $id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit();
}

header("Location: ../admin_dashboard.php?msg=success");
exit();
?>