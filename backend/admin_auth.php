<?php
session_start();
require_once "sqli.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // TEMPORARY BYPASS: Simple string comparison
        if ($pass_input === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            header("Location: ../admin_dashboard.php");
            exit();
        } else {
            die("Debug: Password mismatch. You typed: [$pass_input], DB has: [" . $admin['password'] . "]");
        }
    } else {
        die("Debug: Username not found.");
    }
}
?>