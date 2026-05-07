<?php
// STEP 1: FORCE ERRORS TO SHOW (This stops the Error 500 and shows the real problem)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('UTC'); // Fix timezone warnings

// STEP 2: DATABASE CONNECTION
// Double-check these 4 values match your XAMPP/WAMP settings
require_once "sqli.php";

// If the database is the problem, this will tell us
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// STEP 3: SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

// 1. We ask the database for BOTH the verify_status, live balance, and current name
$query = $conn->query("SELECT fname, lname, verify_status, balance FROM users WHERE id = $userId");

// 2. We store it safely, falling back to session data only if needed
$userData = ($query && $query->num_rows > 0) ? $query->fetch_assoc() : null;

$firstName = $userData ? $userData['fname'] : (isset($_SESSION['user_fname']) ? $_SESSION['user_fname'] : 'User');
$lastName = $userData ? $userData['lname'] : (isset($_SESSION['user_lname']) ? $_SESSION['user_lname'] : '');
$balance = number_format($userData ? $userData['balance'] : 0, 2);
$verifyStatus = $userData ? $userData['verify_status'] : 'Unverified';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firstworldchoice - Dashboard</title>
    <link rel="stylesheet" href="styles/dashboard.css">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <i class="fa-solid fa-building-columns logo-icon"></i>
            <span style="font-weight: 700; font-size: 20px;">FirstWorld</span>
        </div>
        <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-layer-group"></i> Dashboard</a>
        <a href="loan.php" class="nav-item"><i class="fa-solid fa-laptop-code"></i> Loans</a>
        <a href="fund.php" class="nav-item"><i class="fa-solid fa-sliders"></i> Fund Account</a>
        <a href="withdraw.php" class="nav-item"><i class="fa-solid fa-money-bill-transfer"></i> Withdrawal</a>
        <a href="transaction.php" class="nav-item"><i class="fa-solid fa-earth-americas"></i> Transactions</a>
        <a href="backend/logout.php" class="nav-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <main class="main-content" id="main">
        <header>
            <div style="display: flex; align-items: center; gap: 20px;">
                <i class="fa-solid fa-bars" style="font-size: 20px; cursor: pointer;" onclick="funny()"></i>
                <div class="search-bar-top">
                    <h1>Dashboard</h1>
                </div>
            </div>
            <a href="profile.php">
                <div class="user-profile">
                <div>Hello, <strong><?php echo htmlspecialchars($firstName); ?></strong></div>
                <img src="https://via.placeholder.com/35" alt="Profile">
            </div>
            </a>
        </header>

        
            <?php if ($verifyStatus !== 'Verified'): ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #ffeeba;">
                Your account is not yet verified. <a href="info.html">Click here to continue</a>
            </div>
        <?php endif; ?>

        <div class="main-card">
            <p>Welcome <span style="color: red;"><?php echo htmlspecialchars($firstName . " " . $lastName); ?></span></p>
            <div style="font-size: 2rem; font-weight: bold;">$ <?php echo $balance; ?></div>
        </div>

        <div class="payment-methords">
                <a href="withdraw.php" class="payment-method bank-withdraw"><div style="color: red;">↓</div><small>Bank withdrawall</small></a>
                <a href="withdraw.php" class="payment-method gcash-withdraw"><div style="color: blue;">G</div><small>GCash withdrawall</small></a>
                <a href="withdraw.php" class="payment-method paypal-withdraw"><div style="color: #0070ba;">P</div><small>PayPal withdrawall</small></a>
                <a href="withdraw.php" class="payment-method skrill-withdraw"><div style="color: #800080;">S</div><small>Skrill withdrawall</small></a>
            </div>

        <div class="grid-stats">
            <div class="stat-card"><small>Current Balance</small><div>$ <?php echo $balance; ?></div></div>
            <div class="stat-card"><small>Ledger Balance</small><div>$ <?php echo $balance; ?></div></div>
            <div class="stat-card"><small>Available Balance</small><div>$ <?php echo $balance; ?></div></div>
            <div class="stat-card"><small>Refundable Balance</small><div>$ 0.00</div></div>
        </div>

        <div class="overview-grid">
            <div class="color-card" style="background: #a855f7;">
                <div class="circle-progress">0%</div>
                <span>Withdrawals</span>
            </div>
            <div class="color-card" style="background: #22c55e;">
                <div class="circle-progress">0%</div>
                <span>Transfers</span>
            </div>
        </div>

        <div class="overview-grid">
            <div class="color-card" style="background: #22c55e;">
                <div class="circle-progress">0%</div>
                <span>Withdrawals</span>
            </div>
            <div class="color-card" style="background: #a855f7;">
                <div class="circle-progress">0%</div>
                <span>Transfers</span>
            </div>
        </div>
    </main>

    <div class="talk-btn">
        <div style="width: 10px; height: 10px; background: #2ecc71; border-radius: 50%;"></div>
        <a href="talk.php">Talk</a> <i class="fa-solid fa-comment"></i>
    </div>
    <script>
        i = true
        function funny(){
            if (i == true){
                document.getElementById("sidebar").style= "display:none;";
                document.getElementById("main").style = "margin-left:0;";
                i = false
            } else if (i == false){
                document.getElementById("sidebar").style= "display:flex;";
                document.getElementById("main").style = "margin-left:260px;";
                i = true
            }
        }
    </script>
</body>
</html>
