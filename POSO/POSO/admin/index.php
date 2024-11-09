<?php 
// Start the session
session_start();

// Include the database connection file
include 'connection.php'; // Make sure this path is correct
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSO Admin Login</title>
    <link rel="stylesheet" href="/POSO/admin/css/style.css?v=1.0">
    <style>
        /* Fullscreen overlay styling */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }
        
        /* Popup styling */
        .popup {
            background-color: #ffffff;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Styling for success and error icons */
        .popup .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .popup .error-icon { color: #d9534f; }
        .popup .success-icon { color: #28a745; }
        
        .popup h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .popup .error-text { color: #d9534f; }
        .popup .success-text { color: #28a745; }

        .popup p {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .popup button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .popup .retry-btn { background-color: #d9534f; color: white; }
        .popup .continue-btn { background-color: #28a745; color: white; }

        /* Fade-in and fade-out animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        .fade-out {
            animation: fadeOut 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <!-- Error Popup Overlay -->
    <?php if (isset($_SESSION['error'])): ?>
        <div id="errorOverlay" class="overlay fade-in">
            <div id="errorPopup" class="popup">
                <div class="icon error-icon">✖</div>
                <h2 class="error-text">Oh no!</h2>
                <p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <button onclick="closeError()" class="retry-btn">Try Again</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Success Popup Overlay -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="successOverlay" class="overlay fade-in">
            <div id="successPopup" class="popup">
                <div class="icon success-icon">✔</div>
                <h2 class="success-text">Login Successfully!</h2>
                <p><?php echo $_SESSION['success']; ?></p>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> <!-- Display username here -->
                <button onclick="continueToDashboard()" class="continue-btn">Continue</button>
            </div>
        </div>
        <?php unset($_SESSION['success']); // Remove success message after displaying ?>
    <?php endif; ?>

    <br>
    <div class="header-container"> <!-- New div class for the header -->
        <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
        <div>
            <h1>PUBLIC ORDER & SAFETY OFFICE</h1>
            <h2>CITY OF BIÑAN</h2>
        </div>
        <img src="/POSO/images/arman.png" alt="POSO Logo" class="logo">
    </div> 
    <br><br><br>
    <div class="container">
        <div class="login-form">
            <h3>LOGIN</h3>
            <form action="authenticate.php" method="POST">
                <label for="username">USERNAME</label>
                <input type="text" name="username" id="username" required>
                
                <label for="password">PASSWORD</label>
                <input type="password" name="password" id="password" required>
                <a href="#">Forgot Password?</a>
                <br>
                <button type="submit">LOGIN</button>
            </form>
        </div>
    </div>

    <script>
        function closeError() {
            const overlay = document.getElementById('errorOverlay');
            if (overlay) {
                overlay.classList.add('fade-out');
                setTimeout(() => overlay.remove(), 500);
            }
        }

        function continueToDashboard() {
            const overlay = document.getElementById('successOverlay');
            if (overlay) {
                overlay.classList.add('fade-out');
                setTimeout(() => {
                    overlay.remove();
                    window.location.href = 'dashboard.php'; // Redirect to the dashboard or desired page
                }, 500);
            }
        }

        // Automatically close the success popup after 4 seconds
        setTimeout(() => {
            if (document.getElementById('successOverlay')) {
                continueToDashboard();
            }
        }, 4000);
    </script>
</body>
</html>
