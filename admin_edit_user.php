<?php
session_start();
require_once "sqli.php";
require_once "config/currency_helper.php";

$id = $_GET['id'];
$user_data = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $balance = $_POST['balance'];
    $type = $_POST['type'];
    $currency = $_POST['currency'];

    $conn->query("UPDATE users SET fname='$fname', balance='$balance', type='$type', currency='$currency' WHERE id=$id");
    header("Location: admin_dashboard.php#users");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { background: #f4f7f6; padding: 50px; font-family: sans-serif; }
        .edit-box { background: white; padding: 30px; max-width: 500px; margin: auto; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 12px; background: #0d3b36; color: white; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="edit-box">
        <h2>Edit User Profile</h2>
        <form method="POST">
            <label>First Name</label>
            <input type="text" name="fname" value="<?php echo $user_data['fname']; ?>">
            
            <label>Account Balance</label>
            <input type="number" step="0.01" name="balance" value="<?php echo $user_data['balance']; ?>">
            
            <label>Account Type</label>
            <select name="type">
                <option value="savings" <?php if($user_data['type'] == 'savings') echo 'selected'; ?>>Savings</option>
                <option value="checking" <?php if($user_data['type'] == 'checking') echo 'selected'; ?>>Checking</option>
            </select>

            <label>Currency</label>
            <select name="currency">
                <?php 
                $currencies = getAllCurrencies();
                $user_currency = !empty($user_data['currency']) ? $user_data['currency'] : 'USD';
                foreach($currencies as $curr): 
                    $symbol = getCurrencySymbol($curr);
                ?>
                    <option value="<?php echo $curr; ?>" <?php if($user_currency == $curr) echo 'selected'; ?>>
                        <?php echo $curr; ?> (<?php echo $symbol; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Save Changes</button>
            <a href="admin_dashboard.php" style="display:block; text-align:center; margin-top:15px; color:#666;">Cancel</a>
        </form>
    </div>
</body>
</html>