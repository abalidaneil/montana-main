<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.html"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Successful | Firstworldchoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-light: #f8fafb;
            --primary-dark: #0d3b36;
            --primary-green: #2ecc71;
            --text-main: #333;
            --text-muted: #666;
            --sidebar-w: 260px;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styling (Matches your other pages) */
        .sidebar {
            width: var(--sidebar-w);
            background: #fff;
            border-right: 1px solid #eee;
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
        }
        .sidebar h2 { color: var(--primary-dark); margin-bottom: 40px; font-size: 1.5rem; }
        .nav-link {
            display: block; padding: 12px 15px; color: var(--text-muted); text-decoration: none;
            border-radius: 8px; margin-bottom: 10px; font-weight: 500;
        }
        .nav-link:hover { background: #f0fdf4; color: var(--primary-dark); }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-w);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        /* Success Card */
        .success-card {
            background: #fff;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 25px auto;
        }

        h1 { color: var(--primary-dark); font-size: 1.8rem; margin-bottom: 15px; }
        
        p.main-msg { color: var(--text-main); font-size: 1.05rem; line-height: 1.6; margin-bottom: 25px; }

        /* Important Notice Box */
        .notice-box {
            background: #fff8e1;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 4px;
            text-align: left;
            margin-bottom: 30px;
        }
        .notice-box p { margin: 0; font-size: 0.9rem; color: #92400e; line-height: 1.5; }
        .notice-box i { margin-right: 8px; }

        .btn-dashboard {
            display: inline-block;
            background: var(--primary-dark);
            color: #fff;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            box-sizing: border-box;
            transition: background 0.3s ease;
        }
        .btn-dashboard:hover { background: #0a2e28; }

    </style>
</head>
<body>

    <div class="sidebar">
        <h2>FirstWorldChoice</h2>
        <a href="dashboard.php" class="nav-link"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="fund.php" class="nav-link"><i class="fa-solid fa-wallet"></i> Fund Account</a>
        <a href="withdraw.php" class="nav-link"><i class="fa-solid fa-money-bill-transfer"></i> Withdraw</a>
        <a href="chat.php" class="nav-link"><i class="fa-solid fa-headset"></i> Live Support</a>
    </div>

    <div class="main-content">
        <div class="success-card">
            
            <div class="icon-circle">
                <i class="fa-solid fa-check"></i>
            </div>
            
            <h1>Withdrawal Initiated</h1>
            
            <p class="main-msg">
                Your withdrawal request has been successfully submitted. Please allow up to <strong>24 hours</strong> for our financial team to review and process your transaction. You will receive a response shortly.
            </p>

            <div class="notice-box">
                <p>
                    <strong><i class="fa-solid fa-circle-exclamation"></i> Important Action Required:</strong><br> 
                    To ensure a smooth transfer, please make sure you obtain the correct <strong>Routing Number</strong> directly from your assigned agent before the 24-hour review period ends.
                </p>
            </div>

            <a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>

        </div>
    </div>

</body>
</html>