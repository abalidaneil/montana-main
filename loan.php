<?php
session_start();
date_default_timezone_set('UTC'); // Fix timezone warnings
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit(); }

require_once "sqli.php";
$userId = $_SESSION['user_id'];

$userQuery = $conn->prepare("SELECT fname, lname, balance FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userRow = $userResult->fetch_assoc();
$firstName = htmlspecialchars(isset($userRow['fname']) ? $userRow['fname'] : 'User');
$balance = number_format(isset($userRow['balance']) ? $userRow['balance'] : 0, 2);

// Fetch Loan History
$historyQuery = "SELECT * FROM loans WHERE user_id = ? ORDER BY created_at DESC";
$stmtH = $conn->prepare($historyQuery);
$stmtH->bind_param("i", $userId);
$stmtH->execute();
$history = $stmtH->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firstworldchoice - Loans</title>
    <script src="sidebar.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/loan.css">
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

    <div class="main" id="main">

        <header>
            <div style="display: flex; align-items: center; gap: 20px;">
                <i class="fa-solid fa-bars" style="font-size: 20px; cursor: pointer;" onclick="funny()"></i>
                <div class="search-bar-top">
                    <h1>Loan</h1>
                </div>
            </div>
            <a href="profile.php">
                <div class="user-profile">
                <div>Hello, <strong><?php echo htmlspecialchars($firstName); ?></strong></div>
                <img src="https://via.placeholder.com/35" alt="Profile">
            </div>
            </a>
        </header>

        <div class="loan-form">
            <h3>Request a New Loan</h3>
            <form action="backend/process_loan.php" method="POST">
                <input type="number" name="loan_amount" placeholder="Enter Amount" required min="100">
                <button type="submit">Demand Loan</button>
            </form>
        </div>

        <div class="card-grid">
            <div class="card">
                <div><h3>$ <?php echo $balance; ?></h3><p>Current Balance</p></div>
                <i class="fas fa-wallet" style="color:orange"></i>
            </div>
            <div class="card">
                <div><h3>Eligible</h3><p>Status</p></div>
                <i class="fas fa-check-circle" style="color:green"></i>
            </div>
        </div>

        <div class="history-section">
            <h2>Loan History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($history->num_rows > 0): ?>
                        <?php while($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td>$<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td style="color: green; font-weight: bold;"><?php echo $row['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No loan history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>