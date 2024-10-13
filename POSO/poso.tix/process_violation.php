<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'poso';  // Adjust as needed
$username = 'root';
$password = '';  // Adjust your password
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ticket number from the session
$ticket_number = $_SESSION['ticket_number'];

// Get the first name and last name from the URL
$first_name = $_GET['first_name'];  // Pass these values in the URL from the previous page
$last_name = $_GET['last_name']; 

// Fetch the first name and last name from the report table using the ticket number
$sql = "SELECT * FROM report WHERE ticket_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_number);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

if ($report) {
    // If the report exists, check for violations
    $sql_violation = "SELECT * FROM violation WHERE first_name = ? AND last_name = ?";
    $stmt_violation = $conn->prepare($sql_violation);
    $stmt_violation->bind_param("ss", $first_name, $last_name);
    $stmt_violation->execute();
    $violation_result = $stmt_violation->get_result();

    if ($violation_result->num_rows == 0) {
        // If no violations exist for this person, insert the first violation
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $violations = $_POST['violations'];  // Violations from the form
            $total_amount = $_POST['total'];  // Total amount calculated
            $notes = $_POST['notes']; // Capture notes from the form
            
            // Concatenate all violations into a string
            $violations_string = implode(", ", $violations);

            // Debugging output
            // echo "Total Amount: " . htmlspecialchars($total_amount); // Uncomment for debugging

            // Insert the violations into the violation table
            $sql_insert = "INSERT INTO violation (ticket_number, first_name, last_name, first_violation, first_total, notes) VALUES (?, ?, ?, ?, ?, ?)";
            
            // Validate total_amount before binding
            if (!empty($total_amount)) {
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("isssss", $ticket_number, $first_name, $last_name, $violations_string, $total_amount, $notes);
                
                if ($stmt_insert->execute()) {
                    // Redirect to receipt.php
                    header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&first_total=" . urlencode($total_amount));
                    exit();  // Ensure no further code is executed
                } else {
                    echo "Error: " . $stmt_insert->error;
                }
            } else {
                echo "Total amount cannot be empty.";
            }
        }
    } else {
        // If there are already violations, handle accordingly (you can choose to update or not)
        echo "This person already has violations recorded.";
    }
} else {
    echo "No report found for this ticket number.";
}

// Close connection
$stmt->close();
$conn->close();
?>
