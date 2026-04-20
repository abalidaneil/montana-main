<?php
session_start();

// 1. Connection
require_once "sqli.php";

// 2. Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. Security (Optional for testing, but keep it for safety)
if (!isset($_SESSION['admin_id'])) {
    echo "Access Denied. Please log in.";
    exit();
}

// 4. Fetch Data
$verifications = $conn->query("SELECT * FROM users WHERE verify_status = 'Pending'");
$withdrawals = $conn->query("SELECT w.*, u.fname, u.lname FROM withdrawals w JOIN users u ON w.user_id = u.id");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Admin</title>
</head>
<body>

    <h1>Admin Command Center</h1>
    
    <nav>
        <a href="admin_dashboard.php">Refresh Dashboard</a> | 
        <a href="admin_chat_list.php"><b>OPEN SUPPORT CHATS</b></a> | 
        <a href="backend/admin_logout.php">Logout</a>
    </nav>

    <hr>

    <h2>1. Pending Verifications</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>ID Document</th>
                <th>Address Proof</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $verifications->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['fname'] . " " . $user['lname']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><a href="uploads/verify/<?php echo $user['id_document']; ?>" target="_blank">View ID</a></td>
                <td><a href="uploads/verify/<?php echo $user['address_document']; ?>" target="_blank">View Address</a></td>
                <td>
                    <a href="backend/admin_actions.php?action=verify&id=<?php echo $user['id']; ?>&status=Verified">APPROVE</a> | 
                    <a href="backend/admin_actions.php?action=verify&id=<?php echo $user['id']; ?>&status=Unverified">REJECT</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <h2>2. All Withdrawal Requests</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>User</th>
                <th>Amount</th>
                <th>Bank</th>
                <th>Account #</th>
                <th>Routing #</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($w = $withdrawals->fetch_assoc()): ?>
            <tr>
                <td><?php echo $w['fname'] . " " . $w['lname']; ?></td>
                <td>$<?php echo $w['amount']; ?></td>
                <td><?php echo $w['bank_name']; ?></td>
                <td><?php echo $w['account_number']; ?></td>
                <td><?php echo $w['routing_number']; ?></td>
                <td><?php echo $w['status']; ?></td>
                <td>
                    <?php if($w['status'] == 'Pending'): ?>
                        <a href="backend/admin_actions.php?action=withdraw&id=<?php echo $w['id']; ?>&status=Approved">APPROVE PAYOUT</a> | 
                        <a href="backend/admin_actions.php?action=withdraw&id=<?php echo $w['id']; ?>&status=Declined">DECLINE</a>
                    <?php else: ?>
                        Processed
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>