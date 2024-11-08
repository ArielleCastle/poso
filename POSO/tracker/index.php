<?php
// Database connection (update with your own connection details)
$host = 'localhost';
$db = 'poso';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Get the ticket number from the URL
$ticket_number = isset($_GET['ticket_number']) ? $_GET['ticket_number'] : '';

// Initialize variables to hold data
$violator_name = '';
$violation_count = '';
$violations = '';
$amount = '';
$status = '';
$confiscated = '';
$violation_date = '';
$violation_time = '';
$vehicle_type = '';
$plate_number = '';
$vehicle_owner = '';

// Fetch the violation and report data if ticket number exists
if ($ticket_number) {
    // Check ticket number in violation table
    $stmt = $pdo->prepare("SELECT first_name, last_name, first_violation, first_total, status FROM violation WHERE ticket_number = ?");
    $stmt->execute([$ticket_number]);
    $violation = $stmt->fetch();

    if ($violation) {
        $violator_name = $violation['first_name'] . ' ' . $violation['last_name'];
        $violation_count = 'First Violation';
        $violations = $violation['first_violation'];
        $amount = $violation['first_total'];
        $status = $violation['status'];
    }

    // Check ticket number in 2_violation table
    if (!$violator_name) {
        $stmt = $pdo->prepare("SELECT first_name, last_name, second_violation, second_total, status FROM 2_violation WHERE ticket_number = ?");
        $stmt->execute([$ticket_number]);
        $violation = $stmt->fetch();

        if ($violation) {
            $violator_name = $violation['first_name'] . ' ' . $violation['last_name'];
            $violation_count = 'Second Violation';
            $violations = $violation['second_violation'];
            $amount = $violation['second_total'];
            $status = $violation['status'];
        }
    }

    // Check ticket number in 3_violation table
    if (!$violator_name) {
        $stmt = $pdo->prepare("SELECT first_name, last_name, third_violation, third_total, status FROM 3_violation WHERE ticket_number = ?");
        $stmt->execute([$ticket_number]);
        $violation = $stmt->fetch();

        if ($violation) {
            $violator_name = $violation['first_name'] . ' ' . $violation['last_name'];
            $violation_count = '3rd Violation';
            $violations = $violation['third_violation'];
            $amount = $violation['third_total'];
            $status = $violation['status'];
        }
    }

    // Fetch the data from the report table
    if ($violator_name) {
        $stmt = $pdo->prepare("SELECT confiscated, violation_date, violation_time, vehicle_type, plate_number, vehicle_owner FROM report WHERE ticket_number = ?");
        $stmt->execute([$ticket_number]);
        $report = $stmt->fetch();

        if ($report) {
            $confiscated = $report['confiscated'];
            $violation_date = $report['violation_date'];
            $violation_time = $report['violation_time'];
            $vehicle_type = $report['vehicle_type'];
            $plate_number = $report['plate_number'];
            $vehicle_owner = $report['vehicle_owner'];
        }
    }
}

// Clear the search
if (isset($_GET['clear'])) {
    $ticket_number = '';
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSO Violation Tracker</title>
    <link rel="stylesheet" href="/POSO/tracker/css/d_style.css?v=3.0">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="/POSO/images/poso.png" alt="Logo">
        </div>
        <ul></ul>
    </div>

    <div class="main-content">
       <div class="main-content">
        <header>
            <img src="/POSO/images/left.png" alt="City Logo">
            <h1>PUBLIC ORDER & SAFETY OFFICE<br>CITY OF BIÑAN</h1>
            <img src="/POSO/images/arman.png" alt="POSO Logo">
        </header>
<br>
<br>

        <div class="report-container">
            <form method="GET" action="" class="search-filter">
    <div class="search-bar">
        <input type="text" id="ticket_number" name="ticket_number" placeholder="Enter Ticket Number" value="<?php echo $ticket_number; ?>" required>
        <?php if ($ticket_number): ?>
            <a href="?clear=true" class="clear-search">CLEAR</a>
        <?php endif; ?>
    </div>
    <button type="submit">Search</button>
</form>


            <?php if ($violator_name): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Violator's Name</th>
                            <th>Violation Count</th>
                            <th>Violations</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>License Confiscated</th>
                            <th>Violation Date</th>
                            <th>Violation Time</th>
                            <th>Vehicle Type</th>
                            <th>Plate Number</th>
                            <th>Vehicle Owner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $ticket_number; ?></td>
                            <td><?php echo $violator_name; ?></td>
                            <td><?php echo $violation_count; ?></td>
                            <td><?php echo $violations; ?></td>
                            <td><?php echo $amount; ?></td>
                            <td class="<?php echo ($status == 'Paid' ? 'paid' : 'unpaid'); ?>"><?php echo $status; ?></td>
                            <td><?php echo $confiscated; ?></td>
                            <td><?php echo $violation_date; ?></td>
                            <td><?php echo $violation_time; ?></td>
                            <td><?php echo $vehicle_type; ?></td>
                            <td><?php echo $plate_number; ?></td>
                            <td><?php echo $vehicle_owner; ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && !$violator_name): ?>
                <p>No data found for the provided ticket number.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
