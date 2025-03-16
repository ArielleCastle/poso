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
    <link rel="stylesheet" href="/poso/admin/css/sm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>

        <nav class="navbar">
            <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
                    <div>
                        <p class="public" >PUBLIC ORDER & SAFETY OFFICE</p>
                        <p class="city">CITY OF BIÑAN, LAGUNA</p>
                    </div>
                    <img src="/POSO/images/arman.png" alt="POSO Logo" class="logo">

                    <div class="hamburger" id="hamburger-icon">
                       <i class="fa fa-bars"></i>

        </nav>
            <img class="bg" src="/POSO/images/plaza1.jpg" alt="Background Image">


    <?php
            $current_page = basename($_SERVER['PHP_SELF']); // Get the current file name
        ?>

            <div class="sidebar" id="sidebar">
                <div class="logo">
                    <img src="/POSO/images/right.png" alt="POSO Logo">
                </div>
                <ul>
                    <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="report.php" class="<?= $current_page == 'report.php' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li><a href="settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            </header>

            <img class="bg" src="/POSO/images/plaza1.jpg" alt="Background Image">


<form class="sm" method="POST" action="update_report.php?ticket_number=<?= $_GET['ticket_number'] ?>">
    <div class="inside">
        <h2 class="gray" style="display: flex; justify-content: space-between; align-items: center;">
            ORDINANCE INFRACTION TICKET
            <span style="color: red;">NO. <?= htmlspecialchars($report['ticket_number']) ?></span>
        </h2>

        <div class="violator-info">
    <br>
    <h3 class="title">VIOLATOR INFORMATION</h3>
    <br>
    <div class="info-container">
        <div><strong>First Name:</strong></div>
        <div><input type="text" name="first_name" value="<?= htmlspecialchars($report['first_name']) ?>"></div>
    </div>

    <div class="info-container">
        <div><strong>Middle Name:</strong></div>
        <div><input type="text" name="middle_name" value="<?= htmlspecialchars($report['middle_name']) ?>"></div>
    </div>

    <div class="info-container">
        <div><strong>Last Name:</strong></div>
        <div><input type="text" name="last_name" value="<?= htmlspecialchars($report['last_name']) ?>"></div>
    </div>

    <div class="info-container">
        <div><strong>Birthday:</strong></div>
        <div><input type="date" name="dob" value="<?= htmlspecialchars($report['dob']) ?>"></div>
    </div>
    <div class="info-container">
        <div><strong>Address:</strong></div>
        <div><input type="text" name="address" value="<?= htmlspecialchars($report['address']) ?>"></div>
    </div>
    <div class="info-container">
        <div><strong>License Number:</strong></div>
        <div><input type="text" name="license" value="<?= htmlspecialchars($report['license']) ?>" readonly></div>
    </div>
    <div class="info-container">
        <div><strong>Violation Date:</strong></div>
        <div><input type="date" name="violation_date" value="<?= htmlspecialchars($report['violation_date']) ?>" readonly></div>
    </div>
    <div class="info-container">
        <div><strong>Violation Time:</strong></div>
        <div><input type="time" name="violation_time" value="<?= htmlspecialchars($report['violation_time']) ?>" readonly></div>
    </div>
    <div class="info-container">
        <div><strong>License Confiscated:</strong></div>
        <div>
            <select name="confiscated">
                <option value="1" <?= $report['confiscated'] ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= !$report['confiscated'] ? 'selected' : '' ?>>No</option>
            </select>
        </div>
    </div>
</div>

        <div class="place-info">
            <br>
            <h3 class="title">PLACE OF VIOLATION</h3>
            <br>

            <div class="info-container">
                <div><strong>Street:</strong></div>
                <div><input type="text" name="street" value="<?= htmlspecialchars($report['street']) ?>"readonly></div>
            </div>
            <div class="info-container">
                <div><strong>City/Municipality:</strong></div>
                <div><input type="text" name="city" value="<?= htmlspecialchars($report['city']) ?>"readonly></div>
            </div>
            <div class="info-container">
    <div><strong>Vehicle Type:</strong></div>
    <div>
        <select id="vehicle_type" name="vehicle_type" class="form-control">
            <option value="">Select Vehicle Type</option>
            <option value="Passenger Car" <?= $report['vehicle_type'] == 'Passenger Car' ? 'selected' : '' ?>>Passenger Car</option>
            <option value="Motorcycle or Scooter" <?= $report['vehicle_type'] == 'Motorcycle or Scooter' ? 'selected' : '' ?>>Motorcycle or Scooter</option>
            <option value="Public Utility Vehicle" <?= $report['vehicle_type'] == 'Public Utility Vehicle' ? 'selected' : '' ?>>Public Utility Vehicle (PUV)</option>
            <option value="Truck or Delivery Vehicle" <?= $report['vehicle_type'] == 'Truck or Delivery Vehicle' ? 'selected' : '' ?>>Truck or Delivery Vehicle</option>
            <option value="Commercial Vehicle" <?= $report['vehicle_type'] == 'Commercial Vehicle' ? 'selected' : '' ?>>Commercial Vehicle</option>
            <option value="Emergency Vehicle" <?= $report['vehicle_type'] == 'Emergency Vehicle' ? 'selected' : '' ?>>Emergency Vehicle</option>
            <option value="Heavy Equipment" <?= $report['vehicle_type'] == 'Heavy Equipment' ? 'selected' : '' ?>>Heavy Equipment Vehicle</option>
        </select>
    </div>
</div>

            <div class="info-container">
                <div><strong>Plate Number:</strong></div>
                <div><input type="text" name="plate_number" value="<?= htmlspecialchars($report['plate_number']) ?>"readonly></div>
            </div>
            <div class="info-container">
                <div><strong>Registration Number:</strong></div>
                <div><input type="text" name="registration" value="<?= htmlspecialchars($report['registration']) ?>"readonly></div>
            </div>
            <div class="info-container">
                <div><strong>Vehicle Owner:</strong></div>
                <div><input type="text" name="vehicle_owner" value="<?= htmlspecialchars($report['vehicle_owner']) ?>"></div>
            </div>
        </div>

        <div class="penalty-info">
            <br>
            <h3 class="title">PENALTY</h3>
            <br>
           
            <div class="info-container">
                <div><strong>Status:</strong></div>
                <div><input type="text" name="status" value="<?= htmlspecialchars($status) ?>"></div>
            </div>
            <div class="info-container">
                <div><strong>Total Amount:</strong></div>
                <div><input type="number" name="amount" value="<?= htmlspecialchars($amount) ?>" step="0.01"></div>
            </div>
            <div class="info-container">
                <div><strong>Officer In Charge:</strong></div>
                <div><input type="text" name="officer_name" value="<?= htmlspecialchars($officer_name) ?>"readonly></div>
            </div>

            <h3 class="title">SIGNATURES</h3>
            <div class="info-container">
                <div><strong>Officer Signature:</strong></div>
                <div>
                    <?php if ($officer_signature): ?>
                        <img src="data:image/png;base64,<?= base64_encode($officer_signature) ?>" alt="Officer Signature" class="signature-img">
                    <?php else: ?>
                        <?= htmlspecialchars($officer_signature) ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-container">
                <div><strong>Violator's Signature:</strong></div>
                <div>
                    <?php if ($report['violator_signature']): ?>
                        <img src="data:image/png;base64,<?= base64_encode($report['violator_signature']) ?>" alt="Violator Signature" class="signature-img">
                    <?php else: ?>
                        <?= htmlspecialchars($report['violator_signature']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div style="text-align: center;">
    <button type="submit">Update Report</button>
</div>

</form>
    </div>

    <script> 
            const hamburgerIcon = document.getElementById('hamburger-icon');
    const sidebar = document.getElementById('sidebar');

    hamburgerIcon.addEventListener('click', function() {
        // Toggle the "show" class to the sidebar
        sidebar.classList.toggle('show');
    });
    </script>


</body>
</html>
