<?php
// Display errors for debugging just in case
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database Connection
require_once "sqli.php";

$userId = $_SESSION['user_id'];

// Fetch the current user's data
$query = $conn->query("SELECT fname, lname, balance, account_number, verify_status FROM users WHERE id = $userId");

if ($query && $query->num_rows > 0) {
    $userData = $query->fetch_assoc();
    $firstName = $userData['fname'];
    $lastName = $userData['lname'];
    $balance = number_format(isset($userData['balance']) ? $userData['balance'] : 0, 2);
    // If they don't have an account number yet, show a placeholder
    $accountNumber = !empty($userData['account_number']) ? $userData['account_number'] : '0000000000';
    $verifyStatus = $userData['verify_status'];
} else {
    // Fallback data if something goes wrong
    $firstName = "User";
    $lastName = "";
    $balance = "0.00";
    $accountNumber = "0000000000";
    $verifyStatus = "Unverified";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First World Choice - Withdrawal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css">
    <script src="sidebar.js"></script>
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
        <a href="transaction.php" class="nav-item"><i class="fa-solid fa-earth-americas"></i> Transactions <i class="fa-solid fa-chevron-right arrow"></i></a>
        <a href="backend/logout.php" class="nav-item"><i class="fa-solid fa-table-cells-large"></i> Logout <i class="fa-solid fa-chevron-right arrow"></i></a>
    </div>

    <div class="main-content" id="main">
        <header>
            <div style="display: flex; align-items: center; gap: 20px;">
                <i class="fa-solid fa-bars" style="font-size: 20px; cursor: pointer;" onclick="funny()"></i>
                <div class="search-bar-top">
                    <h1>Withdrawal</h1>
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
            <h1></h1>

            <div class="alert-box">
                <?php if ($verifyStatus === 'Unverified'): ?>
                    <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #ffeeba;">
                        <i class="fa-solid fa-circle-exclamation"></i> 
                        Your account is not yet verified. <a href="info.html" style="color: inherit; font-weight: 600; text-decoration: underline;">Click here</a> to continue.
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-visual">
                <div class="card-label">Main Balance</div>
                <div class="card-balance">$ <?php echo $balance; ?></div>
                
                <div class="mastercard-logo">
                    <div class="circle red"></div>
                    <div class="circle yellow"></div>
                </div>

                <div class="card-footer">
                    <div style="display: flex; gap: 30px;">
                        <div>
                            <div class="card-meta-label">Account Type</div>
                            <div class="card-meta-value">Savings Account</div>
                        </div>
                        <div>
                            <div class="card-meta-label">Card Holder</div>
                            <div class="card-meta-value"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></div>
                        </div>
                    </div>
                    <div class="card-acc-num"><?php echo htmlspecialchars($accountNumber); ?></div>
                </div>
            </div>

            <form action="backend/process_withdraw.php" method="POST" class="from-card">
                <div class="form-header">Withdraw Balance</div>
                <div class="form-body">
                    <div class="icon-tabs">
                        <i class="fa-solid fa-money-bill-1 icon-tab"></i>
                        <i class="fa-solid fa-circle-question icon-tab info"></i>
                    </div>

                    <p class="form-warning">
                        You're about to transfer from your account's available balance. Be warned. This action cannot be reversed. Be sure to enter correct details.
                        And get your routing number form you agent
                    </p>

                    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_routing'): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #f5c6cb;">
                            <i class="fa-solid fa-circle-exclamation"></i> 
                            <strong>Invalid Routing Number:</strong> Contact your agent for your routing number. The routing number you entered does not match our records.
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Amount</label>
                        <div class="input-wrapper">
                            <input type="text" name="amount" placeholder="Enter Amount" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Account Number</label>
                        <div class="input-wrapper">
                            <input type="text" name="account_number" placeholder="Account Number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Routing Number (ensure to get your routing number for the agent)</label>
                        <div class="input-wrapper">
                            <input type="text" name="routing_number" placeholder="Routing Number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bank Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="Bank_Name" placeholder="Bank Name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Token Code</label>
                        <div class="input-wrapper">
                            <input type="text" name="Token_code" placeholder="Token Code" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="withdrawBtn">Proceed</button>
                </div>
            </form>
        </div>

        <footer>Copyright © firstworldchoice.com 2026</footer>
    </div>

    <!-- Error Modal Popup -->
    <div id="errorModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="error-icon">
                    <i class="fa-solid fa-exclamation"></i>
                </div>
            </div>
            <div class="modal-body">
                <p>Please review your withdrawal details carefully before proceeding. This action cannot be reversed.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn" onclick="goBackToWithdraw()">Go Back</button>
                <button class="modal-btn modal-btn-confirm" onclick="closeErrorModal()">Continue</button>
            </div>
        </div>
    </div>

    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 40px 20px 20px;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background-color: #ff4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 48px;
            color: white;
            font-weight: bold;
        }

        .modal-body {
            padding: 20px 30px;
        }

        .modal-body p {
            font-size: 16px;
            color: #333;
            margin: 0;
            line-height: 1.6;
        }

        .modal-footer {
            padding: 30px;
        }

        .modal-btn {
            background-color: #333;
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .modal-btn:hover {
            background-color: #555;
        }

        .modal-btn-confirm {
            background-color: #2ecc71;
            margin-left: 10px;
        }

        .modal-btn-confirm:hover {
            background-color: #27ae60;
        }
    </style>

    <div class="talk-btn">
        <div style="width: 10px; height: 10px; background: #2ecc71; border-radius: 50%;"></div>
        <a href="talk.php">Talk</a> <i class="fa-solid fa-comment"></i>
    </div>

    <script>
        // Show the alert modal automatically when the page loads
        window.addEventListener('load', function() {
            showErrorModal();
        });

        // Add form validation before submission
        const form = document.querySelector('.from-card');
        form.addEventListener('submit', function(e) {
            // Validate amount is a positive number
            const amount = document.querySelector('input[name="amount"]').value;
            if (isNaN(amount) || parseFloat(amount) <= 0) {
                e.preventDefault();
                alert('Please enter a valid amount greater than 0');
                return false;
            }
            
            // Form will submit normally to backend/process_withdraw.php
            // Data will be saved to database by the backend handler
            return true;
        });

        function showErrorModal() {
            const modal = document.getElementById('errorModal');
            modal.classList.add('show');
        }

        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            modal.classList.remove('show');
        }

        function goBackToWithdraw() {
            window.location.href = 'withdraw.php';
        }

        // Close modal when clicking outside
        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                // Don't close when clicking outside - user must click a button
                return false;
            }
        });
    </script>

</body>
</html>
