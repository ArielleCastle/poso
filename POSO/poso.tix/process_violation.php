<?php
// Start session
session_start();

// Include the database connection file
include 'connection.php'; // Ensure the path is correct

// Get the ticket number from the session
$ticket_number = $_SESSION['ticket_number'];

// Get the first name and last name from the URL
$first_name = $_GET['first_name'];
$last_name = $_GET['last_name'];

// Fetch the first name and last name from the report table using the ticket number
$sql = "SELECT * FROM report WHERE ticket_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_number);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

if ($report) {
    // Check for previous violations in the violation tables for the given person
    $sql_violation = "SELECT * FROM violation WHERE first_name = ? AND last_name = ?";
    $stmt_violation = $conn->prepare($sql_violation);
    $stmt_violation->bind_param("ss", $first_name, $last_name);
    $stmt_violation->execute();
    $violation_count = $stmt_violation->get_result()->num_rows;

    $sql_violation2 = "SELECT * FROM 2_violation WHERE first_name = ? AND last_name = ?";
    $stmt_violation2 = $conn->prepare($sql_violation2);
    $stmt_violation2->bind_param("ss", $first_name, $last_name);
    $stmt_violation2->execute();
    $violation_count2 = $stmt_violation2->get_result()->num_rows;

    $sql_violation3 = "SELECT * FROM 3_violation WHERE first_name = ? AND last_name = ?";
    $stmt_violation3 = $conn->prepare($sql_violation3);
    $stmt_violation3->bind_param("ss", $first_name, $last_name);
    $stmt_violation3->execute();
    $violation_count3 = $stmt_violation3->get_result()->num_rows;

    // Prepare for new insertion based on the count of existing violations
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $violations = $_POST['violations']; // Array of selected violations
        $total_amount = $_POST['total'];
        $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
        $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;
        $notes = isset($_POST['notes']) ? $_POST['notes'] : null;

        if ($violation_count == 0) {
            // No previous violations found, this will be the first violation
            $sql_insert = "INSERT INTO violation (ticket_number, first_name, last_name, first_violation, first_total, others_violation, others_total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isssssss", $ticket_number, $first_name, $last_name, implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes);
        } elseif ($violation_count == 1 && $violation_count2 == 0) {
            // One violation already exists, this will be the second violation
            $sql_insert = "INSERT INTO 2_violation (ticket_number, first_name, last_name, second_violation, second_total, others_violation, others_total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isssssss", $ticket_number, $first_name, $last_name, implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes);
        } elseif ($violation_count == 1 && $violation_count2 == 1 && $violation_count3 == 0) {
            // Two violations already exist, this will be the third violation
            $sql_insert = "INSERT INTO 3_violation (ticket_number, first_name, last_name, third_violation, third_total, others_violation, others_total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isssssss", $ticket_number, $first_name, $last_name, implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes);
        } else {
            echo "Cannot add more violations.";
            exit();
        }

        // Execute the insertion
        if ($stmt_insert->execute()) {
            header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&total=" . urlencode($total_amount));
            exit();
        } else {
            echo "Error: " . $stmt_insert->error;
        }
    }
} else {
    echo "No report found for this ticket number.";
}

// Close connection
$stmt->close();
$conn->close();
?>
