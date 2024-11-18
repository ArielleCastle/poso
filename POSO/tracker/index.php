<?php
// Database connection (same as before)
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

// Get the ticket number and license number from the URL
$ticket_number = isset($_GET['ticket_number']) ? $_GET['ticket_number'] : '';
$license_number = isset($_GET['license_number']) ? $_GET['license_number'] : '';

// Initialize variables to hold data
$violator_name = '';
$violation_count = '';
$violations = '';
$amount = '';
$status = '';
$confiscated = '';
$violation_date = '';
$violation_time = '';
$license = ''; // Add variable for license number

// Fetch the violation and report data if ticket number or license number exists
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

    // Fetch the data from the report table (including license)
    if ($violator_name) {
        $stmt = $pdo->prepare("SELECT confiscated, violation_date, violation_time, license FROM report WHERE ticket_number = ?");
        $stmt->execute([$ticket_number]);
        $report = $stmt->fetch();

        if ($report) {
            $confiscated = $report['confiscated'];
            $violation_date = $report['violation_date'];
            $violation_time = $report['violation_time'];
            $license = $report['license']; // Fetch the license number
        }
    }
}

// Fetch data based on license number
if ($license_number) {
    // Check license number in report table
    $stmt = $pdo->prepare("SELECT ticket_number, first_name, last_name, first_violation, first_total, status FROM violation WHERE ticket_number IN (SELECT ticket_number FROM report WHERE license = ?)");
    $stmt->execute([$license_number]);
    $violation = $stmt->fetch();

    if ($violation) {
        $violator_name = $violation['first_name'] . ' ' . $violation['last_name'];
        $violation_count = 'First Violation';
        $violations = $violation['first_violation'];
        $amount = $violation['first_total'];
        $status = $violation['status'];
        $ticket_number = $violation['ticket_number'];
    }

    // Fetch the data from the report table (including license)
    if ($violator_name) {
        $stmt = $pdo->prepare("SELECT confiscated, violation_date, violation_time, license FROM report WHERE license = ?");
        $stmt->execute([$license_number]);
        $report = $stmt->fetch();

        if ($report) {
            $confiscated = $report['confiscated'];
            $violation_date = $report['violation_date'];
            $violation_time = $report['violation_time'];
            $license = $report['license']; // Fetch the license number
        }
    }
}

// Clear the search
if (isset($_GET['clear'])) {
    $ticket_number = '';
    $license_number = '';
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
    <link rel="stylesheet" href="/POSO/tracker/css/style.css">
    <title>POSO Violation Tracker</title>
</head>
<body>
    <div class="main-content">
        <header>
            <img src="/POSO/images/left.png" alt="City Logo">
            <h1>PUBLIC ORDER & SAFETY OFFICE<br>CITY OF BIÑAN</h1>
            <img src="/POSO/images/arman.png" alt="POSO Logo">
        </header>
        <br><br>

        <div class="report-container">
            <h1> Please enter a valid ticket number or license number </h1>
            <form method="GET" action="" class="search-filter">
                <div class="search-bar">
                    <input type="text" id="ticket_number" name="ticket_number" placeholder="Ticket Number" value="<?php echo $ticket_number; ?>">
                    <input type="text" id="license_number" name="license_number" placeholder="License Number" value="<?php echo $license_number; ?>">
                    <?php if ($ticket_number || $license_number): ?>
                        <a href="?clear=true" class="clear-search">CLEAR</a>
                    <?php endif; ?>
                    &nbsp;&nbsp;<button type="submit">Search</button>
                </div>
<p>*License Number can be empty if Ticket Number is present.</p>
            </form>
        </div>
    </div>

<?php if ($violator_name): ?>
    <table>
        <thead>
            <tr>
                <th>Ticket Number</th>
                <?php if ($license_number): ?>
                    <th>License Number</th>
                <?php endif; ?>
                <th>Violator's Name</th>
                <th>Violation Count</th>
                <th>Violations</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>License Confiscated</th>
                <th>Violation Date</th>
                <th>Violation Time</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $ticket_number; ?></td>
                <?php if ($license_number): ?>
                    <td><?php echo $license; ?></td>
                <?php endif; ?>
                <td><?php echo $violator_name; ?></td>
                <td><?php echo $violation_count; ?></td>
                <td><?php echo $violations; ?></td>
                <td><?php echo $amount; ?></td>
                <td class="<?php echo ($status == 'Paid' ? 'paid' : 'unpaid'); ?>"><?php echo $status; ?></td>
                <td><?php echo $confiscated; ?></td>
                <td><?php echo $violation_date; ?></td>
                <td><?php echo $violation_time; ?></td>
            </tr>
        </tbody>
    </table>
<?php elseif (($ticket_number || $license_number) && !$violator_name): ?>
    <p>No data found for the provided ticket number or license number.</p>
<?php endif; ?>

</body>
</html>
