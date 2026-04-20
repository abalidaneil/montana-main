<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Chat | Firstworldchoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css">
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

        .chat-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
        }

        .sidebar .logo i {
            color: #d1f366;
            margin-right: 8px;
        }

        .sidebar nav a {
            display: block;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar nav a i {
            margin-right: 12px;
            width: 20px;
        }

        /* Main Chat Area */
        .main-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-header {
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header h2 {
            font-size: 18px;
            font-weight: 600;
        }

        .chat-header .status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .chat-header .status .dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Chat Messages */
        .chat-messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 20px;
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

        .message.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #0d3b36 0%, #1a5f58 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.admin {
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

        .message.user .sender {
            color: #d1f366;
            text-align: right;
        }

        .message.admin .sender {
            color: #0d3b36;
            text-align: left;
        }

        .message .time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 8px;
        }

        .message.user .time {
            text-align: right;
        }

        /* Chat Input */
        .chat-input {
            background: white;
            padding: 25px 30px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .chat-input input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .chat-input input:focus {
            border-color: #0d3b36;
        }

        .chat-input button {
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

        .chat-input button:hover {
            transform: translateY(-2px);
        }

        .chat-input button i {
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
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .main-chat {
                width: 100%;
            }

            .chat-messages {
                padding: 20px;
                gap: 15px;
            }

            .message {
                max-width: 85%;
                font-size: 14px;
                padding: 12px 16px;
            }

            .chat-input {
                padding: 20px;
                gap: 10px;
            }

            .chat-input input {
                padding: 12px 16px;
                font-size: 14px;
            }

            .chat-input button {
                padding: 12px 20px;
                font-size: 14px;
            }

            .chat-header {
                padding: 15px 20px;
            }

            .chat-header h2 {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .chat-messages {
                padding: 15px;
            }

            .message {
                max-width: 90%;
                padding: 10px 14px;
                font-size: 13px;
            }

            .chat-input {
                padding: 15px;
                flex-direction: column;
                gap: 10px;
            }

            .chat-input input {
                width: 100%;
            }

            .chat-input button {
                width: 100%;
                justify-content: center;
            }
        }

        /* Scrollbar Styling */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
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
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <i class="fa-solid fa-building-columns"></i>
                FirstWorld
            </div>
            <nav>
                <a href="dashboard.php"><i class="fa-solid fa-layer-group"></i> Dashboard</a>
                <a href="loan.php"><i class="fa-solid fa-laptop-code"></i> Loans</a>
                <a href="fund.php"><i class="fa-solid fa-sliders"></i> Fund Account</a>
                <a href="withdraw.php"><i class="fa-solid fa-money-bill-transfer"></i> Withdrawal</a>
                <a href="trans.html"><i class="fa-solid fa-earth-americas"></i> Transfer</a>
                <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="chat.php" class="active"><i class="fa-solid fa-comments"></i> Live Support</a>
                <a href="backend/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </nav>
        </aside>

        <!-- Main Chat Area -->
        <main class="main-chat">
            <header class="chat-header">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2>Customer Support Specialist</h2>
                <div class="status">
                    <div class="dot"></div>
                    Online
                </div>
            </header>

            <div class="chat-messages" id="chat-history">
                <!-- Messages will be loaded here -->
            </div>

            <div class="chat-input">
                <input type="text" id="user-msg" placeholder="Describe your issue..." onkeypress="handleKeyPress(event)">
                <button onclick="sendMsg()">
                    <i class="fa-solid fa-paper-plane"></i>
                    Send
                </button>
            </div>
        </main>
    </div>

    <script>
        const userId = "<?php echo $_SESSION['user_id']; ?>";

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMsg();
            }
        }

        function fetchMessages() {
            fetch(`backend/chat_handler.php?action=fetch&user_id=${userId}`)
                .then(r => r.text())
                .then(html => {
                    const history = document.getElementById('chat-history');
                    history.innerHTML = html;
                    history.scrollTop = history.scrollHeight; // Auto-scroll to bottom
                })
                .catch(error => {
                    console.error('Error fetching messages:', error);
                });
        }

        function sendMsg() {
            const input = document.getElementById('user-msg');
            const text = input.value.trim();
            if(!text) return;

            // Show loading state
            const button = input.nextElementSibling;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<div class="loading"></div>';
            button.disabled = true;

            const formData = new URLSearchParams();
            formData.append('user_id', userId);
            formData.append('sender', 'user');
            formData.append('message', text);

            fetch('backend/chat_handler.php?action=send', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    input.value = '';
                    fetchMessages();
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

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                event.target !== toggle &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Initialize
        setInterval(fetchMessages, 3000); // Poll every 3 seconds
        fetchMessages();
    </script>
</body>
</html>