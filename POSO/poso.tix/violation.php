<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'poso';  // Adjust as needed
$username = 'root';
$password = '12345';  // Adjust your password
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ticket number from the session
$ticket_number = $_SESSION['ticket_number'];

// Fetch the first name and last name from the report table using the ticket number
$sql = "SELECT first_name, last_name FROM report WHERE ticket_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_number);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

$first_name = $report['first_name'];
$last_name = $report['last_name'];

// Insert the violation into the violation table
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $violations = $_POST['violations'];  // Assuming violations are an array of selected checkboxes
    $total_amount = $_POST['total'];  // This will be automatically calculated by JavaScript

    $sql_insert = "INSERT INTO violation (ticket_number, first_name, last_name, violations, total_amount) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("issss", $ticket_number, $first_name, $last_name, implode(", ", $violations), $total_amount);
    
    if ($stmt_insert->execute()) {
        echo "Violation recorded successfully!";
    } else {
        echo "Error: " . $stmt_insert->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordinance Infraction Ticket</title>
    <link rel="stylesheet" href="style.css?v=1.0">
    <script>
        // JavaScript function to calculate the total
        function calculateTotal() {
            let checkboxes = document.querySelectorAll('input[name="violations[]"]:checked');
            let total = 0;
            checkboxes.forEach((checkbox) => {
                total += parseFloat(checkbox.getAttribute('data-price'));  // Add the price of each selected checkbox
            });
            document.getElementById('total').value = total.toFixed(2);  // Update the total (fixed to 2 decimal places)
        }
        
        // Add event listeners to all checkboxes
        document.addEventListener('DOMContentLoaded', (event) => {
            let checkboxes = document.querySelectorAll('input[name="violations[]"]');
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', calculateTotal);  // Recalculate total whenever checkbox is changed
            });
        });
    </script>
</head>
<body>
    <div class="ticket-container">
        
        <div class="header-container">
            <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
            <div class="col text-center">
                <p class="title">Traffic Violations</p>
                <p class="city">-City of Biñan, Laguna-</p>
            </div>
            <img src="/POSO/images/arman.png" alt="Right Logo" class="logo">
        </div>

        <div class="ticket-info">
            <p class="ticket-label">Ordinance Infraction Ticket</p>
            <p class="ticket-number">No. <?php echo $ticket_number; ?></p>
        </div>

        <!-- Violation Form -->
<form action="process_violation.php?first_name=<?php echo urlencode($first_name); ?>&last_name=<?php echo urlencode($last_name); ?>" method="POST">
    <!-- Hidden input for ticket number -->
    <input type="hidden" name="ticket_number" value="<?php echo $ticket_number; ?>">


    <!-- Violations list -->
    <div class="gray1">
        <p>You are hereby cited for committing the traffic violations:</p>
    </div>
    <div class="section">

        <input type="checkbox" name="violations[]" value="Illegal Parking - 300" data-price="300"> Illegal Parking <br>
        <input type="checkbox" name="violations[]" value="Overloading - 500" data-price="500"> Overloading <br>
        <input type="checkbox" name="violations[]" value="Speeding - 700" data-price="700"> Speeding<br>
    </div>

    <div class="gray">
        <p>Total Amount:</p>


    </div>
    <div class="section">
        <label for="total">Total:</label>
        <input type="text" id="total" name="total" value="0.00" readonly>
    </div>
<div class="gray">
    <p>Notes:</p>
</div>
<div class="section">
    <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes" value="">
    </div>


    <button type="submit">See Breakdown of Violation</button>
</form>
    </div>
</body>
</html>
