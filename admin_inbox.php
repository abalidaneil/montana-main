<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.html"); exit(); }
require_once "sqli.php";

// Fetch unique users who have messaged
$users = $conn->query("SELECT DISTINCT u.id, u.fname, u.lname FROM users u JOIN messages m ON u.id = m.user_id ORDER BY u.fname ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Support Inbox | Firstworldchoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            height: 100vh;
            overflow: hidden;
        }

        .admin-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar - User List */
        .users-sidebar {
            width: 320px;
            background: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            padding: 25px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sidebar-header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .users-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }

        .user-item {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-item:hover,
        .user-item.active {
            background: linear-gradient(135deg, rgba(13, 59, 54, 0.05) 0%, rgba(26, 95, 88, 0.05) 100%);
            border-left: 4px solid #0d3b36;
            padding-left: 16px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .user-status {
            font-size: 12px;
            color: #6b7280;
        }

        .user-item.active .user-status {
            color: #0d3b36;
            font-weight: 500;
        }

        /* Main Talk Area */
        .talk-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .talk-header {
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .talk-header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .talk-header .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .talk-header .user-details h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .talk-header .user-details .status {
            font-size: 12px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .talk-header .status .dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
        }

        .talk-header .actions {
            display: flex;
            gap: 10px;
        }

        .talk-header .btn {
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s ease;
        }

        .talk-header .btn:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Talk Messages */
        .talk-messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6b7280;
            text-align: center;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 14px;
        }

        .message {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 18px;
            position: relative;
            font-size: 15px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message.admin {
            align-self: flex-end;
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.user {
            align-self: flex-start;
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-bottom-left-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .message .sender {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .message.admin .sender {
            color: #d1f366;
            text-align: right;
        }

        .message.user .sender {
            color: #0d3b36;
            text-align: left;
        }

        .message .time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 8px;
        }

        .message.admin .time {
            text-align: right;
        }

        /* Talk Input */
        .talk-input {
            background: white;
            padding: 25px 30px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .talk-input input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .talk-input input:focus {
            border-color: #0d3b36;
        }

        .talk-input button {
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .talk-input button:hover {
            transform: translateY(-2px);
        }

        .talk-input button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .talk-input button i {
            font-size: 16px;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 10px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .users-sidebar {
                position: fixed;
                left: -320px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }

            .users-sidebar.active {
                left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .talk-main {
                width: 100%;
            }

            .talk-messages {
                padding: 20px;
                gap: 15px;
            }

            .message {
                max-width: 85%;
                font-size: 14px;
                padding: 12px 16px;
            }

            .talk-input {
                padding: 20px;
                gap: 10px;
            }

            .talk-input input {
                padding: 12px 16px;
                font-size: 14px;
            }

            .talk-input button {
                padding: 12px 20px;
                font-size: 14px;
            }

            .talk-header {
                padding: 15px 20px;
            }

            .talk-header .user-details h3 {
                font-size: 14px;
            }

            .sidebar-header {
                padding: 20px;
            }

            .user-item {
                padding: 15px 20px;
                gap: 12px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .talk-messages {
                padding: 15px;
            }

            .message {
                max-width: 90%;
                padding: 10px 14px;
                font-size: 13px;
            }

            .talk-input {
                padding: 15px;
                flex-direction: column;
                gap: 10px;
            }

            .talk-input input {
                width: 100%;
            }

            .talk-input button {
                width: 100%;
                justify-content: center;
            }

            .talk-header .user-info {
                gap: 10px;
            }

            .talk-header .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }

            .user-item {
                padding: 12px 15px;
            }

            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 12px;
            }

            .user-name {
                font-size: 14px;
            }
        }

        /* Scrollbar Styling */
        .users-list::-webkit-scrollbar,
        .talk-messages::-webkit-scrollbar {
            width: 6px;
        }

        .users-list::-webkit-scrollbar-track,
        .talk-messages::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .users-list::-webkit-scrollbar-thumb,
        .talk-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .users-list::-webkit-scrollbar-thumb:hover,
        .talk-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Notification Badge */
        .notification-badge {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-left: auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Users Sidebar -->
        <aside class="users-sidebar" id="usersSidebar">
            <div class="sidebar-header">
                <h2>Support Inbox</h2>
                <div class="subtitle">Manage customer conversations</div>
            </div>

            <div class="users-list">
                <?php
                $hasUsers = false;
                while($user = $users->fetch_assoc()):
                    $hasUsers = true;
                    $initials = strtoupper(substr($user['fname'], 0, 1) . substr($user['lname'], 0, 1));
                ?>
                <div class="user-item" onclick="loadUserTalk(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?>')">
                    <div class="user-avatar"><?php echo $initials; ?></div>
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></div>
                        <div class="user-status">Click to view conversation</div>
                    </div>
                </div>
                <?php endwhile; ?>

                <?php if (!$hasUsers): ?>
                <div class="empty-state" style="padding: 40px 20px;">
                    <i class="fa-solid fa-inbox"></i>
                    <h3>No Conversations</h3>
                    <p>No customers have started conversations yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Main Talk Area -->
        <main class="talk-main">
            <header class="talk-header">
                <div class="user-info">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div id="currentUserInfo" style="display: none;">
                        <div class="user-avatar" id="currentUserAvatar"></div>
                        <div class="user-details">
                            <h3 id="currentUserName"></h3>
                            <div class="status">
                                <div class="dot"></div>
                                Active now
                            </div>
                        </div>
                    </div>
                    <div id="defaultHeader">
                        <h2>Admin Support Center</h2>
                        <div class="status">
                            <div class="dot"></div>
                            Online
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn" onclick="refreshTalk()">
                        <i class="fa-solid fa-refresh"></i>
                    </button>
                </div>
            </header>

            <div class="talk-messages" id="admin-talk-window">
                <div class="empty-state">
                    <i class="fa-solid fa-comments"></i>
                    <h3>Select a Conversation</h3>
                    <p>Choose a customer from the sidebar to start or continue a conversation.</p>
                </div>
            </div>

            <div class="talk-input" id="reply-container" style="display:none;">
                <input type="text" id="admin-reply" placeholder="Type your response..." onkeypress="handleKeyPress(event)">
                <button onclick="sendAdminReply()">
                    <i class="fa-solid fa-paper-plane"></i>
                    Send Reply
                </button>
            </div>
        </main>
    </div>

    <script>
        let activeUserId = null;
        let activeUserName = null;

        function toggleSidebar() {
            document.getElementById('usersSidebar').classList.toggle('active');
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendAdminReply();
            }
        }

        function loadUserTalk(id, name) {
            activeUserId = id;
            activeUserName = name;

            // Update UI
            document.getElementById('reply-container').style.display = 'flex';
            document.getElementById('defaultHeader').style.display = 'none';
            document.getElementById('currentUserInfo').style.display = 'flex';

            // Set user info
            document.getElementById('currentUserName').textContent = name;
            const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
            document.getElementById('currentUserAvatar').textContent = initials;

            // Remove active class from all users
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to clicked user
            event.currentTarget.classList.add('active');

            // Close sidebar on mobile
            if (window.innerWidth <= 768) {
                document.getElementById('usersSidebar').classList.remove('active');
            }

            fetchAdminMessages();
        }

        function fetchAdminMessages() {
            if(!activeUserId) return;

            fetch(`backend/talk_handler.php?action=fetch&user_id=${activeUserId}`)
                .then(r => r.text())
                .then(html => {
                    const win = document.getElementById('admin-talk-window');
                    if (html.trim() === '') {
                        win.innerHTML = `
                            <div class="empty-state">
                                <i class="fa-solid fa-comments"></i>
                                <h3>No Messages Yet</h3>
                                <p>This conversation hasn't started yet. Send the first message!</p>
                            </div>
                        `;
                    } else {
                        win.innerHTML = html;
                        win.scrollTop = win.scrollHeight;
                    }
                })
                .catch(error => {
                    console.error('Error fetching messages:', error);
                });
        }

        function sendAdminReply() {
            const input = document.getElementById('admin-reply');
            const text = input.value.trim();
            if(!text || !activeUserId) return;

            // Show loading state
            const button = input.nextElementSibling;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<div class="loading"></div>';
            button.disabled = true;

            const formData = new URLSearchParams();
            formData.append('user_id', activeUserId);
            formData.append('sender', 'admin');
            formData.append('message', text);

            fetch('backend/talk_handler.php?action=send', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    input.value = '';
                    fetchAdminMessages();
                } else {
                    alert('Failed to send message. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please check your connection.');
            })
            .finally(() => {
                // Reset button state
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }

        function refreshTalk() {
            if (activeUserId) {
                fetchAdminMessages();
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('usersSidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                event.target !== toggle &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Initialize
        setInterval(() => {
            if (activeUserId) {
                fetchAdminMessages();
            }
        }, 3000); // Poll every 3 seconds
    </script>
</body>
</html>
