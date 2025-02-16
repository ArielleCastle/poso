<?php 
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSO Ticketing System</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/POSO/poso.tix/css/index.css">

</head>
<body class="d-flex flex-column align-items-center justify-content-center vh-100">
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

    <!-- Header Section -->
    <nav class="navbar">
    <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
            <div>
                <p class="public" >PUBLIC ORDER & SAFETY OFFICE</p>
                <p class="city">CITY OF BIÑAN, LAGUNA</p>
            </div>
            <img src="/POSO/images/arman.png" alt="POSO Logo" class="logo">
    </nav>


    <div class="container" style="max-width: 295px;">
            <div class="login-form">
                <h3>LOGIN</h3>
                <form class="type" action="authenticate.php" method="POST">
                    <label for="username"> Username</label>
                    <input type="text" name="username" id="username" required>
                    
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                    <a href="#">Forgot Password?</a>
                    <br>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>


    <!-- <div class="container" style="max-width: 295px;">
        <div class="card shadow-sm p-4">
            <form action="authenticate.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">USERNAME</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">PASSWORD</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="text-end mb-3">
                    <a href="#" class="small text-decoration-none">Forgot Password?</a>
                </div>
<br>
                <button type="submit" class="btn btn-primary w-100">LOGIN</button>
            </form>
        </div>
    </div> -->

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
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
                    window.location.href = 'menu.php'; // Redirect to the dashboard or desired page
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
