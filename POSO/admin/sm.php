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
        r.violation_time,
        r.first_name,
        r.middle_name,
        r.last_name,
        r.dob,
        r.address,
        r.license,
        r.registration,
        r.vehicle_owner,
        r.confiscated,
        r.street,               -- Street of violation
        r.city,                 -- City/Municipality
        r.vehicle_type,         -- Vehicle Type
        r.plate_number,         -- Plate Number
        r.signature AS violator_signature  -- Signature of violator (BLOB)
    FROM 
        report AS r
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

// Initialize penalty-related variables
$status = $amount = $officer_name = $officer_signature = '';

// Check in violation table for penalty information
$stmt = $conn->prepare("
    SELECT 
        v.status,
        v.first_total,
        v.others_total,
        v.o_firstname,
        v.o_lastname,
        v.o_signature
    FROM 
        violation AS v
    WHERE 
        v.ticket_number = :ticket_number
");
$stmt->bindParam(':ticket_number', $ticket_number);
$stmt->execute();
$violation = $stmt->fetch(PDO::FETCH_ASSOC);

if ($violation) {
    $status = $violation['status'];
    $amount = $violation['first_total'] + $violation['others_total'];
    $officer_name = $violation['o_firstname'] . ' ' . $violation['o_lastname'];
    $officer_signature = $violation['o_signature']; // BLOB file
}

// Check in 2_violation table for penalty information
$stmt = $conn->prepare("
    SELECT 
        v2.status,
        v2.second_total,
        v2.others_total,
        v2.2o_firstname,
        v2.2o_lastname,
        v2.2o_signature
    FROM 
        2_violation AS v2
    WHERE 
        v2.ticket_number = :ticket_number
");
$stmt->bindParam(':ticket_number', $ticket_number);
$stmt->execute();
$violation2 = $stmt->fetch(PDO::FETCH_ASSOC);

if ($violation2) {
    $status = $violation2['status'];
    $amount = $violation2['second_total'] + $violation2['others_total'];
    $officer_name = $violation2['2o_firstname'] . ' ' . $violation2['2o_lastname'];
    $officer_signature = $violation2['2o_signature']; // BLOB file
}

// Check in 3_violation table for penalty information
$stmt = $conn->prepare("
    SELECT 
        v3.status,
        v3.third_total,
        v3.others_total,
        v3.3o_firstname,
        v3.3o_lastname,
        v3.3o_signature
    FROM 
        3_violation AS v3
    WHERE 
        v3.ticket_number = :ticket_number
");
$stmt->bindParam(':ticket_number', $ticket_number);
$stmt->execute();
$violation3 = $stmt->fetch(PDO::FETCH_ASSOC);

if ($violation3) {
    $status = $violation3['status'];
    $amount = $violation3['third_total'] + $violation3['others_total'];
    $officer_name = $violation3['3o_firstname'] . ' ' . $violation3['3o_lastname'];
    $officer_signature = $violation3['3o_signature']; // BLOB file
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="/POSO/admin/css/d_style.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .signature-img {
            width: 150px; /* Set the desired width */
            height: auto; /* Maintain aspect ratio */
            max-height: 100px; /* Ensure the height doesn't exceed this */
        }
    </style>
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

        <h2>Report for Ticket #<?= htmlspecialchars($report['ticket_number']) ?></h2>

        <div class="violator-info">   
            <br>
            <h3>VIOLATOR INFORMATION</h3>
            <br>
            <p><strong>Violator's Name:</strong> <?= htmlspecialchars($report['first_name'] . ' ' . $report['middle_name'] . ' ' . $report['last_name']) ?></p>
            <p><strong>Birthday:</strong> <?= htmlspecialchars($report['dob']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($report['address']) ?></p>
            <p><strong>License Number:</strong> <?= htmlspecialchars($report['license']) ?></p>
            <p><strong>Violation Date:</strong> <?= htmlspecialchars($report['violation_date']) ?></p>
            <p><strong>Violation Time:</strong> <?= htmlspecialchars($report['violation_time']) ?></p>
            <p><strong>License Confiscated:</strong> <?= htmlspecialchars($report['confiscated'] ? 'Yes' : 'No') ?></p>
        </div>

        <div class="place-info">
            <br>
            <h3>PLACE OF VIOLATION</h3>
            <br>
            <p><strong>Street:</strong> <?= htmlspecialchars($report['street']) ?></p>
            <p><strong>City/Municipality:</strong> <?= htmlspecialchars($report['city']) ?></p>
            <p><strong>Vehicle Type:</strong> <?= htmlspecialchars($report['vehicle_type']) ?></p>
            <p><strong>Plate Number:</strong> <?= htmlspecialchars($report['plate_number']) ?></p>
            <p><strong>Registration Number:</strong> <?= htmlspecialchars($report['registration']) ?></p>
            <p><strong>Vehicle Owner:</strong> <?= htmlspecialchars($report['vehicle_owner']) ?></p>
        </div>

        <div class="penalty-info">
            <br>
            <h3>PENALTY</h3>
            <br>
            <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
            <p><strong>Total Amount:</strong> <?= htmlspecialchars($amount) ?></p>
            <p><strong>Officer In Charge:</strong> <?= htmlspecialchars($officer_name) ?></p>
            <p><strong>Officer Signature:</strong> 
                <?php if ($officer_signature): ?>
                    <img src="data:image/png;base64,<?= base64_encode($officer_signature) ?>" alt="Officer Signature" class="signature-img">
                <?php endif; ?>
            </p>
            <p><strong>Violator's Signature:</strong> 
                <?php if ($report['violator_signature']): ?>
                    <img src="data:image/png;base64,<?= base64_encode($report['violator_signature']) ?>" alt="Violator Signature" class="signature-img">
                <?php endif; ?>
            </p>
        </div>

        <a href="report.php">Back to Reports</a>
    </div>

</body>
</html>
