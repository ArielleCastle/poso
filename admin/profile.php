<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

try {
    // Fetch logged-in user data using PDO
    $query = "SELECT * FROM login WHERE ID = :user_id LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user data was found
    if (!$user) {
        throw new Exception("User data not found.");
    }

    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['tmp_name']) {
            $image = file_get_contents($_FILES['profile_image']['tmp_name']);
            $updateQuery = "UPDATE login SET firstname = :firstname, lastname = :lastname, username = :username, email = :email, password = :password, image = :image WHERE ID = :user_id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':image', $image, PDO::PARAM_LOB);
        } else {
            $updateQuery = "UPDATE login SET firstname = :firstname, lastname = :lastname, username = :username, email = :email, password = :password WHERE ID = :user_id";
            $updateStmt = $conn->prepare($updateQuery);
        }

        $updateStmt->bindParam(':firstname', $firstname);
        $updateStmt->bindParam(':lastname', $lastname);
        $updateStmt->bindParam(':username', $username);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':password', $password);
        $updateStmt->bindParam(':user_id', $_SESSION['user_id']);
        $updateStmt->execute();

        // Refresh user data after update
        header("Location: profile.php");
        exit();
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        .toggle-switch {
            margin-top: 10px;
        }
        .update-notification {
            margin-top: 10px;
            color: green;
            font-weight: bold;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }
        input:checked + .slider {
            background-color: #4CAF50;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Admin Profile</h2>
    <div class="card p-3">
        <!-- Toggle Switch -->
        <div class="toggle-switch text-right">
            <label class="switch">
                <input type="checkbox" id="toggleUpdate" onclick="toggleUpdate()">
                <span class="slider round"></span>
            </label>
            <span>Toggle Update Mode</span>
            <div id="updateNotification" class="update-notification" style="display: none;">
                You're updating your profile
            </div>
        </div>

        <!-- Profile Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 text-center">
                    <label>Profile Picture:</label>
                    <div>
                        <?php if ($user['image']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($user['image']) ?>" alt="Profile Picture" class="profile-image mb-2">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/150" alt="Profile Picture" class="profile-image mb-2">
                        <?php endif; ?>
                    </div>
                    <input type="file" name="profile_image" class="form-control-file mb-3" id="profileImage" style="display: none;">
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" name="firstname" class="form-control mb-2" value="<?= htmlspecialchars($user['firstname']) ?>" required id="firstname" disabled>
                        <input type="text" name="lastname" class="form-control mb-2" value="<?= htmlspecialchars($user['lastname']) ?>" required id="lastname" disabled>
                    </div>
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" class="form-control mb-2" value="<?= htmlspecialchars($user['username']) ?>" required id="username" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control mb-2" value="<?= htmlspecialchars($user['email']) ?>" required id="email" disabled>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control mb-2" value="<?= htmlspecialchars($user['password']) ?>" required id="password" disabled>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary" id="updateButton" style="display: none;">Update Profile</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Toggle Update Mode</h5>
            </div>
            <div class="modal-body">
                You switched off Update Mode. Selecting "Confirm" will not save any changes.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="cancelToggleOff()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmToggleOff()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleUpdate() {
        const isUpdating = document.getElementById("toggleUpdate").checked;
        if (!isUpdating) {
            $('#confirmationModal').modal('show');
        } else {
            enableUpdateMode(true);
        }
    }

    function enableUpdateMode(enable) {
        const elements = ["firstname", "lastname", "username", "email", "password", "profileImage"];
        elements.forEach(id => document.getElementById(id).disabled = !enable);
        document.getElementById("updateButton").style.display = enable ? "block" : "none";
        document.getElementById("profileImage").style.display = enable ? "block" : "none";
        document.getElementById("updateNotification").style.display = enable ? "block" : "none";
    }

    function confirmToggleOff() {
        enableUpdateMode(false);
        $('#confirmationModal').modal('hide');
    }

    function cancelToggleOff() {
        document.getElementById("toggleUpdate").checked = true;
        $('#confirmationModal').modal('hide');
    }
</script>
</body>
</html>
