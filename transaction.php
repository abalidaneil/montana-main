<?php
session_start();
date_default_timezone_set('UTC');

// Security: Redirect to login if the session is not active
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database Connection
require_once "sqli.php";

// Fetch user information
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

// Fetch all transactions (loans, withdrawals, and deposits)
$transactionQuery = "
    SELECT 
        'Loan' as transaction_type,
        id,
        amount,
        status,
        created_at
    FROM loans
    WHERE user_id = ?
    
    UNION ALL
    
    SELECT 
        'Withdrawal' as transaction_type,
        id,
        amount,
        status,
        created_at
    FROM withdrawals
    WHERE user_id = ?
    
    UNION ALL
    
    SELECT 
        'Deposit' as transaction_type,
        id,
        amount,
        status,
        created_at
    FROM deposits
    WHERE user_id = ?
    
    ORDER BY created_at DESC
";

$stmt = $conn->prepare($transactionQuery);
$stmt->bind_param("iii", $userId, $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First World Choice - Transaction History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/trans.css">
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .status-approved { color: #27ae60; font-weight: 600; }
        .status-pending { color: #f39c12; font-weight: 600; }
        .status-declined { color: #e74c3c; font-weight: 600; }
        td { padding: 0.9375rem 0.625rem; border-bottom: 1px solid #eee; font-size: 0.875rem; }
        tbody tr:hover { background-color: #f9f9f9; }
    </style>
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
                    <i class="fa-solid fa-magnifying-glass" style="color: #aaa;"></i>
                    <input type="text" placeholder="Search transactions..." id="searchInput">
                </div>
            </div>
            <a href="profile.php" style="text-decoration: none;">
                <div class="user-profile">
                    <span>Hello, <strong><?php echo $firstName; ?></strong></span>
                    <img src="https://via.placeholder.com/35" alt="Profile">
                </div>
            </a>
        </header>

        <div class="content-body">
            <h1>Transaction History</h1>

            <div class="alert-box">
                <!-- Account status message can go here -->
            </div>

            <div class="table-card">
                <div class="table-controls">
                    <div class="show-entries">
                        Show 
                        <select id="entriesPerPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select> 
                        entries
                    </div>
                    <div class="table-search">
                        Account: <span style="font-weight: 600;"><?php echo $accNumber; ?></span>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>SNo. <i class="fa-solid fa-sort"></i></th>
                            <th>Transaction Type <i class="fa-solid fa-sort"></i></th>
                            <th>Amount <i class="fa-solid fa-sort"></i></th>
                            <th>Status <i class="fa-solid fa-sort"></i></th>
                            <th>Date <i class="fa-solid fa-sort"></i></th>
                        </tr>
                    </thead>
                    <tbody id="transactionTableBody">
                        <?php if (count($transactions) === 0): ?>
                            <tr>
                                <td colspan="5" class="empty-row">No transactions available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $index => $transaction): ?>
                                <tr class="transaction-row">
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($transaction['transaction_type']); ?></strong>
                                    </td>
                                    <td>$ <?php echo number_format($transaction['amount'], 2); ?></td>
                                    <td>
                                        <span class="status-<?php echo strtolower(str_replace(' ', '-', $transaction['status'])); ?>">
                                            <?php echo htmlspecialchars($transaction['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y - H:i', strtotime($transaction['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="table-footer">
                    <div>Showing <span id="showingCount">1</span> to <span id="toCount"><?php echo count($transactions); ?></span> of <span id="totalCount"><?php echo count($transactions); ?></span> entries</div>
                    <div class="pagination">
                        <button class="page-btn" id="prevBtn">Previous</button>
                        <button class="page-btn" id="nextBtn">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <footer>Copyright © firstworldchoice.com 2026</footer>
    </div>

    <div class="talk-btn">
        <div class="status-dot"></div>
        <a href="talk.php"> Talk </a> <i class="fa-solid fa-comment"></i>
    </div>

    <script src="sidebar.js"></script>
    <script>
        // Pagination and search functionality
        const transactionRows = document.querySelectorAll('.transaction-row');
        const entriesPerPageSelect = document.getElementById('entriesPerPage');
        const searchInput = document.getElementById('searchInput');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const tableBody = document.getElementById('transactionTableBody');
        
        let currentPage = 1;
        let entriesPerPage = 10;
        let filteredRows = Array.from(transactionRows);

        function displayTable() {
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = startIndex + entriesPerPage;

            // Hide all rows
            transactionRows.forEach(row => row.style.display = 'none');

            // Show relevant rows
            filteredRows.slice(startIndex, endIndex).forEach(row => {
                row.style.display = '';
            });

            // Update pagination info
            const totalPages = Math.ceil(filteredRows.length / entriesPerPage);
            document.getElementById('showingCount').textContent = filteredRows.length === 0 ? 0 : startIndex + 1;
            document.getElementById('toCount').textContent = Math.min(endIndex, filteredRows.length);
            document.getElementById('totalCount').textContent = filteredRows.length;

            // Enable/disable pagination buttons
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || filteredRows.length === 0;
        }

        function filterTransactions() {
            const searchTerm = searchInput.value.toLowerCase();
            filteredRows = Array.from(transactionRows).filter(row => {
                return row.textContent.toLowerCase().includes(searchTerm);
            });
            currentPage = 1;
            displayTable();
        }

        entriesPerPageSelect.addEventListener('change', (e) => {
            entriesPerPage = parseInt(e.target.value);
            currentPage = 1;
            displayTable();
        });

        searchInput.addEventListener('keyup', filterTransactions);

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayTable();
            }
        });

        nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredRows.length / entriesPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                displayTable();
            }
        });

        // Initial display
        displayTable();
    </script>
</body>
</html>