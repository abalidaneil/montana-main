<?php
session_start();
// Database Connection
require_once "sqli.php";

if (!isset($_SESSION['user_id'])) { header("Location: ../login.html"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $loanAmount = floatval($_POST['loan_amount']);

    if ($loanAmount > 0) {
        // Start a transaction to ensure both steps happen or neither does
        $conn->autocommit(false);

        try {
            // 1. Record the loan in the loans table
            $stmt1 = $conn->prepare("INSERT INTO loans (user_id, amount) VALUES (?, ?)");
            $stmt1->bind_param("id", $userId, $loanAmount);
            $stmt1->execute();

            // 2. Update the user's balance in the users table
            $stmt2 = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt2->bind_param("di", $loanAmount, $userId);
            $stmt2->execute();

            // Commit the changes
            $conn->commit();
            $conn->autocommit(true);
            header("Location: ../loan.php?status=success");
        } catch (Exception $e) {
            $conn->rollback();
            $conn->autocommit(true);
            header("Location: ../loan.php?status=error");
        }
    }
}
?>