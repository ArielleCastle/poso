<?php
include('connection.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

function fetchUsers($table) {
    global $conn;
    $query = "SELECT * FROM $table";
    $stmt = $conn->query($query);
    return $stmt;
}

function deleteUser($table, $id) {
    global $conn;
    $query = "DELETE FROM $table WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

function addUser($table, $firstname, $lastname, $username, $email, $password, $signature) {
    global $conn;
    $query = "INSERT INTO $table (firstname, lastname, username, email, password, signature)
                VALUES (:firstname, :lastname, :username, :email, :password, :signature)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':signature', $signature);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $signature = file_get_contents($_FILES['signature']['tmp_name']);
        $table = ($role == 'admin') ? 'login' : 'hh_login';

        if (addUser($table, $firstname, $lastname, $username, $email, $password, $signature)) {
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }

    if (isset($_POST['delete_user'])) {
        $id = $_POST['user_id'];
        $role = $_POST['role'];
        $table = ($role == 'admin') ? 'login' : 'hh_login';

        if (deleteUser($table, $id)) {
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
</head>
<body>
    <h2>Add User</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>First Name:</label>
        <input type="text" name="firstname" required>
        <label>Last Name:</label>
        <input type="text" name="lastname" required>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Signature:</label>
        <input type="file" name="signature" accept="image/*" required>
        <label>Role:</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="officer">Officer</option>
        </select>
        <button type="submit" name="add_user">Add User</button>
    </form>

    <h2>User Management</h2>
    <table border="1">
        <tr>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Password</th>
            <th>Signature</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php
        $admins = fetchUsers('login');
        while ($row = $admins->fetch(PDO::FETCH_ASSOC)) {
            $fullname = $row['firstname'] . ' ' . $row['lastname'];
            echo "<tr>
                <td>$fullname</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$row['password']}</td>
                <td><img src='data:image/jpeg;base64," . base64_encode($row['signature']) . "' height='50'/></td>
                <td>Admin</td>
                <td>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='user_id' value='{$row['ID']}'>
                        <input type='hidden' name='role' value='admin'>
                        <button type='submit' name='delete_user'>Delete</button>
                    </form>
                </td>
            </tr>";
        }

        $officers = fetchUsers('hh_login');
        while ($row = $officers->fetch(PDO::FETCH_ASSOC)) {
            $fullname = $row['firstname'] . ' ' . $row['lastname'];
            echo "<tr>
                <td>$fullname</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$row['password']}</td>
                <td><img src='data:image/jpeg;base64," . base64_encode($row['signature']) . "' height='50'/></td>
                <td>Officer</td>
                <td>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='user_id' value='{$row['ID']}'>
                        <input type='hidden' name='role' value='officer'>
                        <button type='submit' name='delete_user'>Delete</button>
                    </form>
                </td>
            </tr>";
        }
        ?>
    </table>
</body>
</html>