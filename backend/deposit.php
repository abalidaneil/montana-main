<?php
session_start();
require_once "../sqli.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']); // Get the amount from the form

    if ($amount > 0) {
        // Update user balance
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $amount, $userId);

        if ($stmt->execute()) {
            // Log deposit transaction
            $depositSql = "INSERT INTO deposits (user_id, amount, status) VALUES (?, ?, 'Approved')";
            $depositStmt = $conn->prepare($depositSql);
            $depositStmt->bind_param("id", $userId, $amount);
            $depositStmt->execute();
            $depositStmt->close();
            
            // Success: Redirect back to fund page with a success message
            header("Location: ../fund.php?status=success");
        } else {
            header("Location: ../fund.php?status=error");
        }
        $stmt->close();
    } else {
        header("Location: ../fund.php?status=invalid_amount");
    }
}
$conn->close();
?>