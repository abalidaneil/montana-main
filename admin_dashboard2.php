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
        :root { --sidebar-bg: #0d3b36; --main-bg: #f4f7f6; --accent: #d1f366; }
        body { margin: 0; display: flex; font-family: 'Inter', sans-serif; background: var(--main-bg); }
        
        /* Sidebar Navigation */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; padding: 20px; display: unset; }
        .sidebar h2 { color: var(--accent); margin-bottom: 40px; }
        .nav-link { display: block; padding: 15px; color: #a0b1ad; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }

        /* Main Content Area */
        .content { margin-left: 300px; padding: 40px; width: calc(100% - 300px); }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border-bottom: 4px solid var(--sidebar-bg); }

        /* CRUD Tables */
        .section-card { background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; color: #666; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #f9f9f9; font-size: 0.9rem; }

        /* Action Buttons */
        .btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: bold; }
        .btn-edit { background: #e0f2fe; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-approve { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <h2>F.W.C Admin</h2>
        <a href="#users" class="nav-link active"><i class="fa fa-users"></i> Users</a>
        <a href="test.php" class="nav-link"><i class="fa fa-hand-holding-dollar"></i> Loans</a>
        <a href="admin_inbox.php" class="nav-link"><i class="fa fa-money-bill-transfer"></i> Chats</a>
        <a href="backend/logout.php" class="nav-link" style="margin-top:50px;"><i class="fa fa-sign-out"></i> Logout</a>
    </div>
    
    <div class="content" id="main">
        <i class="fa-solid fa-bars" style="font-size: 20px; cursor: pointer;" onclick="funny()"></i>
        <button onclick="funny()">click me</button>
        <div class="stats-row">
            <div class="stat-card"><h3>Total Users</h3><p><?php echo $user_count; ?></p></div>
            <div class="stat-card"><h3>Global Balance</h3><p>$<?php echo number_format($total_on_site, 2); ?></p></div>
            <div class="stat-card"><h3>System Status</h3><p style="color:green">Online</p></div>
        </div>

        <div class="section-card" id="users">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Manage Users</h2>
                <a href="admin_add_user.php" class="btn btn-approve">+ Add New User</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>code</th>
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
                        <td><?php echo $row['login_code'];?></td>
                        <td><strong>$<?php echo number_format($row['balance'], 2); ?></strong></td>
                        <td>
                            <a href="admin_edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Update</a>
                            <a href="backend/admin_delete.php?type=user&id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete user and all their history?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php
// Fetch withdrawals and the names of the users who made them
$sql = "SELECT w.*, u.fname, u.lname, u.email 
        FROM withdrawals w 
        JOIN users u ON w.user_id = u.id 
        ORDER BY w.created_at DESC";
$withdrawals = $conn->query($sql);
?>

<div class="admin-card">
    <h2>Withdrawal Requests</h2>
    <table class="admin-table">
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
            <?php while($row = $withdrawals->fetch_assoc()): ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                <td><?php echo $row['fname']; ?> (<?php echo $row['email']; ?>)</td>
                <td><strong>$<?php echo number_format($row['amount'], 2); ?></strong></td>
                <td><?php echo $row['bank_name']; ?><br><small>Acc: <?php echo $row['account_number']; ?></small></td>
                <td><code><?php echo $row['routing_number']; ?></code></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if($row['status'] == 'Pending'): ?>
                        <a href="backend/admin_actions.php?action=withdraw&id=<?php echo $row['id']; ?>&status=Approved" class="btn-sm btn-approve">Approve</a>
                        <a href="backend/admin_actions.php?action=withdraw&id=<?php echo $row['id']; ?>&status=Declined" class="btn-sm btn-reject">Decline</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

        <div style="margin-top: 50px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="color: #0d3b36; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <i class="fa-solid fa-user-shield"></i> Pending Identity Verifications
    </h2>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left;">
                <!-- <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">User Details</th> -->
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">ID Document (Front)</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Address Proof (Back)</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Management</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch users waiting for approval
            $v_query = "SELECT id, user_id, image1, image2 FROM verify_status";
            $v_result = $conn->query($v_query);

            if ($v_result->num_rows > 0) {
                while($verif = $v_result->fetch_assoc()) {
                    echo "<tr>";
                    
                    // Link to the first image
                    echo "<td style='padding: 12px; border-bottom: 1px solid #eee;'>
                            <a href='view_image.php?id={$verif['id']}&type=1' target='_blank' style='color: #007bff; text-decoration: none;'>
                                <i class='fa-solid fa-image'></i> View Image 1
                            </a>
                          </td>";
                    
                    // Link to the second image
                    echo "<td style='padding: 12px; border-bottom: 1px solid #eee;'>
                            <a href='view_image.php?id={$verif['id']}&type=2' target='_blank' style='color: #007bff; text-decoration: none;'>
                                <i class='fa-solid fa-image'></i> View Image 2
                            </a>
                          </td>";
                    
                    // Approve/Reject buttons
                    echo "<td style='padding: 12px; border-bottom: 1px solid #eee;'>
                            <a href='backend/admin_actions.php?action=verify&id={$verif['id']}&status=Verified' 
                               style='background: #2ecc71; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-right: 5px;'>APPROVE</a>
                            <a href='backend/admin_actions.php?action=verify&id={$verif['id']}&status=Unverified' 
                               style='background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;'>REJECT</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: #999;'>No pending verifications found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

        <div class="section-card" id="loans">
            <h2>Loan Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($l = $loans->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $l['fname'].' '.$l['lname']; ?></td>
                        <td>$<?php echo number_format($l['amount'], 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($l['created_at'])); ?></td>
                        <td><span style="color:orange"><?php echo $l['status']; ?></span></td>
                        <td><span style="color:orange"><?php echo $l['login_code']; ?></span></td>
                        <td>
                            <a href="backend/admin_actions.php?action=approve_loan&id=<?php echo $l['id']; ?>" class="btn btn-approve">Verify</a>
                            <a href="backend/admin_delete.php?type=loan&id=<?php echo $l['id']; ?>" class="btn btn-delete">Clear</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="sidebar.js"></script>
    <script>
        i = true
        function funny(){
            if (i == true){
                document.getElementById("sidebar").style= "display:none;";
                document.getElementById("main").style = "margin-left:0;";
                i = false
                console.log(i)
            } else if (i == false){
                document.getElementById("sidebar").style= "display:flex;";
                document.getElementById("main").style = "margin-left:300px;";
                i = true
                console.log(i)
            }
        }
    </script>
</body>
</html>
