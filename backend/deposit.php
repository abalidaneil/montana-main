<?php
session_start();
include 'db_connection.php'; // Ensure you have your DB config in a shared file

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']); // Get the amount from the form

    if ($amount > 0) {
        // The SQL logic to INCREASE the balance
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $amount, $userId);

        if ($stmt->execute()) {
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