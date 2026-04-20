<?php
session_start();
date_default_timezone_set('UTC'); // Fix timezone warnings
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.html"); exit(); }

require_once "sqli.php";

// --- READ: Fetching all the data for the "Comprehensive" view ---
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
$verify = $conn->query("SELECT * FROM verify_status ORDER BY id DESC");
$loans = $conn->query("SELECT l.*, u.fname, u.lname FROM loans l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
$withdraws = $conn->query("SELECT w.*, u.fname, u.lname FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC");

// Stats for the top cards
$total_on_site = $conn->query("SELECT SUM(balance) as total FROM users")->fetch_assoc()['total'];
$user_count = $users->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firstworldchoice | Super Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        :root { --sidebar-bg: #0d3b36; --main-bg: #f4f7f6; --accent: #d1f366; }
        
        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: var(--main-bg);
            display: flex;
            flex-direction: column;
        }
        
        /* Sidebar Navigation */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            position: fixed; 
            left: 0;
            top: 0;
            padding: 20px; 
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar h2 { color: var(--accent); margin-bottom: 40px; margin-top: 0; }
        
        .nav-link { 
            display: block; 
            padding: 15px; 
            color: #a0b1ad; 
            text-decoration: none; 
            border-radius: 8px; 
            margin-bottom: 5px; 
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .nav-link:hover, .nav-link.active { 
            background: rgba(255,255,255,0.1); 
            color: white; 
        }

        /* Main Content Area */
        .content { 
            margin-left: 300px; 
            padding: 20px 30px;
            width: calc(100% - 300px);
            transition: all 0.3s ease;
        }

        .hamburger-btn {
            display: none;
            background: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 20px;
            color: var(--sidebar-bg);
            margin-bottom: 15px;
            align-items: center;
            gap: 10px;
            font-weight: bold;
        }

        .hamburger-btn:hover {
            background: #f0f0f0;
        }

        /* Stats Grid */
        .stats-row { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .stat-card { 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            border-bottom: 4px solid var(--sidebar-bg);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            color: #666;
        }

        .stat-card p {
            margin: 0;
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--sidebar-bg);
        }

        /* CRUD Tables */
        .section-card { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        }

        .section-card h2 {
            margin-top: 0;
            color: #0d3b36;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Table Wrapper for horizontal scroll */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        
        th { 
            text-align: left; 
            padding: 12px; 
            border-bottom: 2px solid #eee; 
            color: #666; 
            font-size: 0.85rem;
            background: #f9f9f9;
            font-weight: 600;
        }
        
        td { 
            padding: 12px; 
            border-bottom: 1px solid #f9f9f9; 
            font-size: 0.9rem; 
        }

        /* Action Buttons */
        .btn { 
            padding: 6px 12px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-size: 0.75rem; 
            font-weight: bold;
            display: inline-block;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .btn-edit { background: #e0f2fe; color: #0369a1; }
        .btn-edit:hover { background: #bae6fd; }

        .btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-delete:hover { background: #fecaca; }

        .btn-approve { background: #dcfce7; color: #166534; }
        .btn-approve:hover { background: #bbf7d0; }

        .btn-reject { background: #fee2e2; color: #b91c1c; }
        .btn-reject:hover { background: #fecaca; }

        .btn-sm {
            padding: 4px 8px;
            font-size: 0.7rem;
        }

        /* Status Badges */
        .status-pending { color: #f59e0b; }
        .status-approved { color: #10b981; }
        .status-declined { color: #ef4444; }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                display: flex;
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                max-height: 70vh;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar h2 {
                margin-bottom: 20px;
            }

            .content { 
                margin-left: 0;
                width: 100%;
                padding: 10px 15px;
                margin-top: 50px;
            }

            .hamburger-btn {
                display: flex;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 999;
            }

            .stats-row { 
                grid-template-columns: 1fr;
                gap: 15px;
                margin-bottom: 20px;
            }

            .section-card { 
                padding: 15px; 
                margin-bottom: 20px;
                border-radius: 8px;
            }

            .section-card h2 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }

            .card-header {
                flex-direction: column;
                align-items: stretch;
            }

            .card-header .btn {
                width: 100%;
                text-align: center;
            }

            .table-wrapper {
                overflow-x: auto;
                margin: 0 -15px;
                padding: 0 15px;
            }

            table { 
                font-size: 0.8rem;
            }

            th { 
                padding: 8px;
                font-size: 0.75rem;
            }
            
            td { 
                padding: 8px;
                font-size: 0.8rem;
            }

            .btn {
                padding: 5px 8px;
                font-size: 0.65rem;
                margin-right: 3px;
                margin-bottom: 3px;
            }

            .btn-sm {
                padding: 3px 6px;
                font-size: 0.6rem;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 10px;
                margin-top: 55px;
            }

            .hamburger-btn {
                left: 5px;
                top: 8px;
                padding: 8px 10px;
                font-size: 18px;
            }

            .stat-card {
                padding: 15px;
                border-radius: 8px;
            }

            .stat-card h3 {
                font-size: 0.8rem;
                margin-bottom: 8px;
            }

            .stat-card p {
                font-size: 1.5rem;
            }

            .section-card {
                padding: 12px;
                margin-bottom: 15px;
            }

            .section-card h2 {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }

            table {
                font-size: 0.75rem;
            }

            th, td {
                padding: 6px;
            }

            .btn {
                padding: 4px 6px;
                font-size: 0.6rem;
            }

            /* Hide less important columns on very small screens */
            th:nth-child(n+4), 
            td:nth-child(n+4) {
                display: none;
            }

            .btn-action-col {
                display: block;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>

    <button class="hamburger-btn" id="hamburger" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i> Menu
    </button>
    
    <div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <h2><i class="fas fa-crown"></i> F.W.C Admin</h2>
        <a href="#users" class="nav-link active" onclick="toggleSidebar()"><i class="fa fa-users"></i> Users</a>
        <a href="test.php" class="nav-link" onclick="toggleSidebar()"><i class="fa fa-hand-holding-dollar"></i> Loans</a>
        <a href="admin_inbox.php" class="nav-link" onclick="toggleSidebar()"><i class="fa fa-money-bill-transfer"></i> Chats</a>
        <a href="backend/logout.php" class="nav-link" style="margin-top:30px;"><i class="fa fa-sign-out"></i> Logout</a>
    </div>
    
    <div class="content" id="main">
        <div class="stats-row">
            <div class="stat-card">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-dollar-sign"></i> Global Balance</h3>
                <p>$<?php echo number_format($total_on_site, 2); ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-circle"></i> System Status</h3>
                <p style="color:green; margin: 0;"><i class="fas fa-check-circle"></i> Online</p>
            </div>
        </div>

        <!-- Users Section -->
        <div class="section-card" id="users">
            <div class="card-header">
                <h2>Manage Users</h2>
                <a href="admin_add_user.php" class="btn btn-approve">+ Add New User</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Code</th>
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo $row['fname'].' '.$row['lname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><code><?php echo $row['login_code'];?></code></td>
                            <td><strong>$<?php echo number_format($row['balance'], 2); ?></strong></td>
                            <td style="white-space: nowrap;">
                                <a href="admin_edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Update</a>
                                <a href="backend/admin_delete.php?type=user&id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete user and all their history?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Withdrawal Requests Section -->
        <div class="section-card">
            <h2><i class="fas fa-money-bill"></i> Withdrawal Requests</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Bank Info</th>
                            <th>Routing #</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT w.*, u.fname, u.lname, u.email 
                                FROM withdrawals w 
                                JOIN users u ON w.user_id = u.id 
                                ORDER BY w.created_at DESC";
                        $withdrawals = $conn->query($sql);
                        
                        while($row = $withdrawals->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td><?php echo $row['fname']; ?><br><small><?php echo $row['email']; ?></small></td>
                            <td><strong>$<?php echo number_format($row['amount'], 2); ?></strong></td>
                            <td><?php echo $row['bank_name']; ?><br><small>Acc: <?php echo $row['account_number']; ?></small></td>
                            <td><code><?php echo $row['routing_number']; ?></code></td>
                            <td><span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                            <td style="white-space: nowrap;">
                                <?php if($row['status'] == 'Pending'): ?>
                                    <a href="backend/admin_actions.php?action=withdraw&id=<?php echo $row['id']; ?>&status=Approved" class="btn btn-approve btn-sm">Approve</a>
                                    <a href="backend/admin_actions.php?action=withdraw&id=<?php echo $row['id']; ?>&status=Declined" class="btn btn-reject btn-sm">Decline</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Loan Requests Section -->
        <div class="section-card" id="loans">
            <h2><i class="fas fa-file-contract"></i> Loan Requests</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($l = $loans->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $l['fname'].' '.$l['lname']; ?></td>
                            <td>$<?php echo number_format($l['amount'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($l['created_at'])); ?></td>
                            <td><span class="status-<?php echo strtolower($l['status']); ?>"><?php echo $l['status']; ?></span></td>
                            <td><code><?php echo $l['login_code']; ?></code></td>
                            <td style="white-space: nowrap;">
                                <a href="backend/admin_actions.php?action=approve_loan&id=<?php echo $l['id']; ?>" class="btn btn-approve btn-sm">Verify</a>
                                <a href="backend/admin_delete.php?type=loan&id=<?php echo $l['id']; ?>" class="btn btn-delete btn-sm">Clear</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Identity Verifications Section -->
        <div class="section-card">
            <h2><i class="fas fa-user-shield"></i> Pending Identity Verifications</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID Document (Front)</th>
                            <th>Address Proof (Back)</th>
                            <th>Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $v_query = "SELECT id, user_id, image1, image2 FROM verify_status";
                        $v_result = $conn->query($v_query);

                        if ($v_result->num_rows > 0) {
                            while($verif = $v_result->fetch_assoc()) {
                                echo "<tr>";
                                
                                echo "<td>
                                        <a href='view_image.php?id={$verif['id']}&type=1' target='_blank' style='color: #007bff; text-decoration: none;'>
                                            <i class='fa-solid fa-image'></i> View
                                        </a>
                                    </td>";
                                
                                echo "<td>
                                        <a href='view_image.php?id={$verif['id']}&type=2' target='_blank' style='color: #007bff; text-decoration: none;'>
                                            <i class='fa-solid fa-image'></i> View
                                        </a>
                                    </td>";
                                
                                echo "<td style='white-space: nowrap;'>
                                        <a href='backend/admin_actions.php?action=verify&id={$verif['id']}&status=Verified' 
                                           class='btn btn-approve btn-sm'>APPROVE</a>
                                        <a href='backend/admin_actions.php?action=verify&id={$verif['id']}&status=Unverified' 
                                           class='btn btn-reject btn-sm'>REJECT</a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='padding: 20px; text-align: center; color: #999;'>No pending verifications found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close sidebar when clicking on nav links (except logout)
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            if (!link.href.includes('logout.php')) {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        toggleSidebar();
                    }
                });
            }
        });

        // Close sidebar on window resize if it becomes larger
        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>

