<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection file
include 'connection.php';

// Get the ticket number from the query parameter
$ticket_number = isset($_GET['ticket_number']) ? $_GET['ticket_number'] : '';

// Fetch report details based on the ticket number
$stmt = $conn->prepare("
    SELECT 
        r.ticket_number,
        r.violation_date,
        r.first_name,
        r.last_name,
        r.address,
        r.vehicle_type,
        r.license,
        r.registration,
        r.vehicle_owner,
        COALESCE(v.STATUS, v2.STATUS, v3.STATUS) AS STATUS,
        CASE 
            WHEN v.ticket_number IS NOT NULL THEN 'First Violation'
            WHEN v2.ticket_number IS NOT NULL THEN 'Second Violation'
            WHEN v3.ticket_number IS NOT NULL THEN 'Third Violation'
            ELSE 'Unknown'
        END AS violation_level,
        CASE
            WHEN v.ticket_number IS NOT NULL THEN (v.first_total + v.others_total)  -- First Violation
            WHEN v2.ticket_number IS NOT NULL THEN (v2.second_total + v2.others_total) -- Second Violation
            WHEN v3.ticket_number IS NOT NULL THEN (v3.third_total + v3.others_total) -- Third Violation
            ELSE 0  -- Default if no violation found
        END AS penalty_amount
    FROM 
        report AS r
    LEFT JOIN 
        violation AS v ON r.ticket_number = v.ticket_number
    LEFT JOIN 
        2_violation AS v2 ON r.ticket_number = v2.ticket_number
    LEFT JOIN 
        3_violation AS v3 ON r.ticket_number = v3.ticket_number
    WHERE 
        r.ticket_number = :ticket_number
");
$stmt->bindParam(':ticket_number', $ticket_number);
$stmt->execute();




$report = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if no report is found
if (!$report) {
    header("Location: report.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
 <link rel="icon" href="/POSO/images/poso.png" type="image/png">
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
        </header>        <div class="report-details">
            <h2>Report for Ticket #<?= htmlspecialchars($report['ticket_number']) ?></h2>
            <p><strong>Violator's Name:</strong> <?= htmlspecialchars($report['first_name'] . ' ' . $report['last_name']) ?></p>
            <p><strong>Date of Violation:</strong> <?= htmlspecialchars($report['violation_date']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($report['address']) ?></p>
            <p><strong>Vehicle Type:</strong> <?= htmlspecialchars($report['vehicle_type']) ?></p>
            <p><strong>License Number:</strong> <?= htmlspecialchars($report['license']) ?></p>
            <p><strong>Registration Number:</strong> <?= htmlspecialchars($report['registration']) ?></p>
            <p><strong>Vehicle Owner:</strong> <?= htmlspecialchars($report['vehicle_owner']) ?></p>
            <p><strong>Violation Level:</strong> <?= htmlspecialchars($report['violation_level']) ?></p>
            <p><strong>Payment Status:</strong> <?= htmlspecialchars($report['STATUS']) ?></p>
            <p><strong>Penalty Amount:</strong> <?= htmlspecialchars($report['penalty_amount']) ?></p>
            <a href="report.php">Back to Reports</a>
    </div>
</body>
</html>
