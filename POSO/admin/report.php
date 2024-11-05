<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection file
include 'connection.php';

// Fetch user data from login table
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, image FROM login WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$username = "ADMIN 123";
$imageData = null;

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $row['username'];
    $imageData = $row['image'];
}

// Fetch report data with violation status and number
$reports = [];
$stmt = $conn->prepare("
    SELECT 
        r.ticket_number,
        r.violation_date,
        r.first_name,
        r.last_name,
        COALESCE(v.STATUS, v2.STATUS, v3.STATUS) AS payment_status,
        CASE 
            WHEN v.ticket_number IS NOT NULL THEN 'First Violation'
            WHEN v2.ticket_number IS NOT NULL THEN 'Second Violation'
            WHEN v3.ticket_number IS NOT NULL THEN 'Third Violation'
            ELSE 'Unknown'
        END AS violation_level
    FROM 
        report AS r
    LEFT JOIN 
        violation AS v ON r.ticket_number = v.ticket_number
    LEFT JOIN 
        2_violation AS v2 ON r.ticket_number = v2.ticket_number
    LEFT JOIN 
        3_violation AS v3 ON r.ticket_number = v3.ticket_number
");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="/POSO/admin/css/d_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="/POSO/images/right.png" alt="POSO Logo">
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="report.php" class="active"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <img src="/POSO/images/left.png" alt="City Logo">
            <h1>PUBLIC ORDER & SAFETY OFFICE<br>CITY OF BIÑAN</h1>
            <img src="/POSO/images/arman.png" alt="POSO Logo">
        </header>
        <br><br>
        <div class="report-container">
            <div class="search-filter">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-filter"></i> Filters</button>
                <button><i class="fas fa-calendar-alt"></i> Date</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Ticket Number</th>
                        <th>Violation Number</th>
                        <th>Date</th>
                        <th>Violator's Name</th>
                        <th>Payment Status 
                            <span class="tooltip">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltiptext">Payment Status is verified via Biñan City Treasury and cannot be modified by any POSO admin.</span>
                            </span>
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($reports as $report) {
                        $violatorName = $report['first_name'] . ' ' . $report['last_name'];
                        $paymentStatus = $report['payment_status'] ? strtoupper($report['payment_status']) : 'UNPAID';
                        echo "<tr>
                                <td>{$report['ticket_number']}</td>
                                <td>{$report['violation_level']}</td>
                                <td>{$report['violation_date']}</td>
                                <td>{$violatorName}</td>
                                <td class='".strtolower($paymentStatus)."'>{$paymentStatus}</td>
                                <td>
                                    <a href='#'><i class='fas fa-eye'></i></a>
                                    <a href='#'><i class='fas fa-trash-alt'></i></a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
