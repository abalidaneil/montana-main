<?php
// STEP 1: FORCE ERRORS TO SHOW (This stops the Error 500 and shows the real problem)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('UTC'); // Fix timezone warnings

// STEP 2: DATABASE CONNECTION
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

// Fetch comprehensive user data
$query = $conn->query("SELECT fname, lname, email, phone, gender, address, country, state, zip, type, discription, balance, verify_status, account_number FROM users WHERE id = $userId");

$userData = ($query && $query->num_rows > 0) ? $query->fetch_assoc() : null;

if (!$userData) {
    die("User data not found.");
}

// Extract user data with fallbacks
$firstName = $userData['fname'];
$lastName = $userData['lname'];
$fullName = $firstName . ' ' . $lastName;
$email = $userData['email'];
$phone = $userData['phone'] ?: '';
$gender = $userData['gender'] ?: '';
$address = $userData['address'] ?: '';
$country = $userData['country'] ?: '';
$state = $userData['state'] ?: '';
$zip = $userData['zip'] ?: '';
$accountType = $userData['type'] ?: 'savings';
$description = $userData['discription'] ?: '';
$balance = number_format($userData['balance'], 2);
$verifyStatus = $userData['verify_status'];
$accountNumber = $userData['account_number'] ?: '';

// Capitalize account type for display
$accountTypeDisplay = ucfirst($accountType) . ' Account';

// Determine verification message
$verificationMessage = '';
if ($verifyStatus !== 'Verified') {
    $verificationMessage = 'Your account is not yet verified. <a href="info.html" style="color: inherit; font-weight: 600;">Click here</a> to continue';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First World Choice - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/profile.css">
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
        <a href="trans.html" class="nav-item"><i class="fa-solid fa-earth-americas"></i> Transfer <i class="fa-solid fa-chevron-right arrow"></i></a>
        <a href="backend/logout.php" class="nav-item"><i class="fa-solid fa-table-cells-large"></i> Logout <i class="fa-solid fa-chevron-right arrow"></i></a>
    </div>

    <div class="main-content" id="main">
        <header>
            <div style="display: flex; align-items: center; gap: 20px;">
                <i class="fa-solid fa-bars" style="font-size: 20px; cursor: pointer;" onclick="funny()"></i>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass" style="color: #aaa;"></i>
                    <input type="text" placeholder="Search here...">
                </div>
            </div>
            <div class="user-profile">
                <span>Hello, <strong><?php echo htmlspecialchars($firstName); ?></strong></span>
                <img src="https://via.placeholder.com/35" alt="Profile">
            </div>
        </header>

        <div class="content-body">
            <h1>Profile</h1>

            <?php if ($verificationMessage): ?>
            <div class="alert-box">
                <?php echo $verificationMessage; ?>
            </div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="cover-photo"></div>
                <div class="profile-summary">
                    <img src="https://via.placeholder.com/100" class="pfp-main" alt="User">
                    <div class="summary-info">
                        <div>
                            <div class="name"><?php echo htmlspecialchars($fullName); ?></div>
                            <div class="label"><?php echo htmlspecialchars($accountTypeDisplay); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 15px; color: #555;"><?php echo htmlspecialchars($email); ?></div>
                            <div class="label">Email</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-card">
                <div class="tabs">
                    <div class="tab active">Account</div>
                    <div class="tab">Authentication</div>
                    <div class="tab">Setting</div>
                    <div class="tab">Change Avatar</div>
                </div>

                <div class="details-content">
                    <div class="section-block">
                        <div class="section-title">Account Description</div>
                        <div class="section-value"><?php echo htmlspecialchars($description ?: 'No description provided'); ?></div>
                    </div>

                    <div class="section-block">
                        <div class="section-title">Account Number</div>
                        <div class="section-value"><?php echo htmlspecialchars($accountNumber ?: 'Not assigned'); ?></div>
                    </div>

                    <div class="section-block">
                        <div class="section-title">Account Type</div>
                        <div class="section-value"><?php echo htmlspecialchars($accountTypeDisplay); ?></div>
                    </div>

                    <div class="section-block" style="margin-top: 40px;">
                        <div class="section-title" style="font-size: 16px;">Personal Information</div>
                        <div class="info-list">
                            <div class="info-row"><div class="info-key">Name :</div> <div class="info-val"><?php echo htmlspecialchars($fullName); ?></div></div>
                            <div class="info-row"><div class="info-key">Email :</div> <div class="info-val"><?php echo htmlspecialchars($email); ?></div></div>
                            <div class="info-row"><div class="info-key">Phone Number :</div> <div class="info-val"><?php echo htmlspecialchars($phone ?: 'Not provided'); ?></div></div>
                            <div class="info-row"><div class="info-key">Nationality :</div> <div class="info-val"><?php echo htmlspecialchars($country ?: 'Not provided'); ?></div></div>
                            <div class="info-row"><div class="info-key">DOB :</div> <div class="info-val">Not available</div></div>
                            <div class="info-row"><div class="info-key">Occupation :</div> <div class="info-val">Not provided</div></div>
                            <div class="info-row"><div class="info-key">Address :</div> <div class="info-val"><?php echo htmlspecialchars($address ?: 'Not provided'); ?></div></div>
                            <div class="info-row"><div class="info-key">Gender :</div> <div class="info-val"><?php echo htmlspecialchars($gender ?: 'Not provided'); ?></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer>Copyright © firstworldchoice.com 2026</footer>
    </div>

    <div class="talk-btn">
        <div style="width: 10px; height: 10px; background: #2ecc71; border-radius: 50%;"></div>
        Chat <i class="fa-solid fa-comment"></i>
    </div>

</body>
</html>
