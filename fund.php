<?php
session_start();
// Security: Redirect to login if the session is not active
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database Connection
require_once "sqli.php";

// Fetch fresh data for the logged-in user
$userId = $_SESSION['user_id'];
$query = "SELECT fname, lname, balance, type, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Format variables for display
$fullName = htmlspecialchars($user['fname'] . " " . $user['lname']);
$firstName = htmlspecialchars($user['fname']);
$balance = number_format($user['balance'], 2);
$accType = htmlspecialchars($user['type']);
$accNumber = htmlspecialchars($user['phone']); // Using phone as account number
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First World Choice - Fund Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/fund.css">
    <script src="sidebar.js"></script>
     <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <i class="fa-solid fa-building-columns logo-icon"></i>
            <span style="font-weight: 700; font-size: 20px;">FirstWorld</span>
        </div>
        <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-layer-group"></i> Dashboard <i class="fa-solid fa-chevron-right arrow"></i></a>
        <a href="loan.php" class="nav-item"><i class="fa-solid fa-laptop-code"></i> Loans <i class="fa-solid fa-chevron-right arrow"></i></a>
        <a href="fund.php" class="nav-item"><i class="fa-solid fa-sliders"></i> Fund Account <i class="fa-solid fa-chevron-right arrow"></i></a>
        <a href="withdraw.php" class="nav-item"><i class="fa-solid fa-sliders"></i> Withdrawal <i class="fa-solid fa-chevron-down arrow"></i></a>
        <a href="trans.html" class="nav-item"><i class="fa-solid fa-earth-americas"></i> Transfer <i class="fa-solid fa-chevron-right arrow"></i></a>
        <a href="backend/logout.php" class="nav-item"><i class="fa-solid fa-table-cells-large"></i> Logout <i class="fa-solid fa-chevron-right arrow"></i></a>
    </div>

    <div class="main-content" id="main">
        <header>
            <div style="display: flex; align-items: center; gap: 20px;">
                <i class="fa-solid fa-bars" style="font-size: 20px; cursor: pointer;" onclick="funny()"></i>
                <div class="search-bar-top">
                    <h1>Fund Account</h1>
                </div>
            </div>
            <a href="profile.php">
                <div class="user-profile">
                <div>Hello, <strong><?php echo htmlspecialchars($firstName); ?></strong></div>
                <img src="https://via.placeholder.com/35" alt="Profile">
            </div>
            </a>
        </header>
        

        <div class="content-body">
            
           
            <div class="card">
                <div class="balance-info">
                    <i class="fa-solid fa-wallet wallet-icon"></i>
                    <div class="balance-details">
                        <h3>Main Balance</h3>
                        <div class="balance-amount">$ <?php echo $balance; ?></div>
                    </div>
                    <div class="account-meta">
                        <div>
                            <div class="meta-label">Acc Type</div>
                            <div class="meta-value"><?php echo $accType; ?></div>
                        </div>
                        <div>
                            <div class="meta-label">Account Owner</div>
                            <div class="meta-value"><?php echo $fullName; ?></div>
                        </div>
                        <div>
                            <div class="meta-label">Account ID</div>
                            <div class="meta-value"><?php echo $accNumber; ?></div>
                        </div>
                    </div>
                </div>
                <div class="progress-container"><div class="progress-fill"></div></div>
                <div class="deposit-actions">
                    <button class="btn-outline">+Crypto Deposit</button>
                    <button class="btn-outline">+Fiat Deposit</button>
                </div>
            </div>
        </div>
        <footer>Copyright © firstworldchoice.com 2026</footer>
    </div>

    <div class="talk-btn">
        <div style="width: 10px; height: 10px; background: #2ecc71; border-radius: 50%;"></div>
        <a href="talk.php">Talk</a> <i class="fa-solid fa-comment"></i>
    </div>
</body>
</html>
