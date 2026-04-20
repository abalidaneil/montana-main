<?php
session_start();
if (!isset($_SESSION['admin_id'])) { die("Access Denied"); }
require_once "sqli.php";

$action = $_GET['action'];
$id = $_GET['id'];
$status = $_GET['status'];

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

header("Location: ../admin_dashboard.php?msg=success");
exit();
?>