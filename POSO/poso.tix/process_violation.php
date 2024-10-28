<?php
// Start session
session_start();

// Include the database connection file
include 'connection.php'; // Ensure the path is correct

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
    // Check for violations in the violation table for the given person
    $sql_violation = "SELECT * FROM violation WHERE first_name = ? AND last_name = ?";
    $stmt_violation = $conn->prepare($sql_violation);
    $stmt_violation->bind_param("ss", $first_name, $last_name);
    $stmt_violation->execute();
    $violation_result = $stmt_violation->get_result();

    // Check if the person has violations
    if ($violation_result->num_rows == 0) {
        // No violations, insert into first_violation
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $violations = $_POST['violations'];  // Violations from the form
            $total_amount = $_POST['total'];  // Total amount calculated
            $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
            $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;
            $notes = isset($_POST['notes']) ? $_POST['notes'] : null; // Get notes from the form

            // Insert into violation table with first_violation and notes
            $sql_insert = "INSERT INTO violation (ticket_number, first_name, last_name, first_violation, first_total, others_violation, others_total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isssssss", $ticket_number, $first_name, $last_name, implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes);

            if ($stmt_insert->execute()) {
                header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&first_total=" . urlencode($total_amount));
                exit();  // Ensure no further code is executed
            } else {
                echo "Error: " . $stmt_insert->error;
            }
        }
    } else {
        // If the person has violations, check the specific violations
        $existing_violation = $violation_result->fetch_assoc();
        $first_violation = $existing_violation['first_violation'];
        $second_violation = $existing_violation['second_violation'];
        $third_violation = $existing_violation['third_violation'];

        if (empty($first_violation)) {
            // Insert into first_violation
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $violations = $_POST['violations'];  // Violations from the form
                $total_amount = $_POST['total'];  // Total amount calculated
                $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
                $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;
                $notes = isset($_POST['notes']) ? $_POST['notes'] : null; // Get notes from the form

                // Update first_violation
                $sql_update = "UPDATE violation SET first_violation = ?, first_total = ?, others_violation = ?, others_total = ?, notes = ? WHERE first_name = ? AND last_name = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sssssss", implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes, $first_name, $last_name);

                if ($stmt_update->execute()) {
                    header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&first_total=" . urlencode($total_amount));
                    exit();  // Ensure no further code is executed
                } else {
                    echo "Error: " . $stmt_update->error;
                }
            }
        } elseif (empty($second_violation)) {
            // Insert into second_violation
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $violations = $_POST['violations'];  // Violations from the form
                $total_amount = $_POST['total'];  // Total amount calculated
                $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
                $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;
                $notes = isset($_POST['notes']) ? $_POST['notes'] : null; // Get notes from the form

                // Update second_violation
                $sql_update = "UPDATE violation SET second_violation = ?, second_total = ?, others_violation = ?, others_total = ?, notes = ? WHERE first_name = ? AND last_name = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sssssss", implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes, $first_name, $last_name);

                if ($stmt_update->execute()) {
                    header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&second_total=" . urlencode($total_amount));
                    exit();  // Ensure no further code is executed
                } else {
                    echo "Error: " . $stmt_update->error;
                }
            }
        } elseif (empty($third_violation)) {
            // Insert into third_violation
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $violations = $_POST['violations'];  // Violations from the form
                $total_amount = $_POST['total'];  // Total amount calculated
                $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
                $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;
                $notes = isset($_POST['notes']) ? $_POST['notes'] : null; // Get notes from the form

                // Update third_violation
                $sql_update = "UPDATE violation SET third_violation = ?, third_total = ?, others_violation = ?, others_total = ?, notes = ? WHERE first_name = ? AND last_name = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sssssss", implode(", ", $violations), $total_amount, $others_violation, $others_total, $notes, $first_name, $last_name);

                if ($stmt_update->execute()) {
                    header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&third_total=" . urlencode($total_amount));
                    exit();  // Ensure no further code is executed
                } else {
                    echo "Error: " . $stmt_update->error;
                }
            }
        } else {
            // If all violations are filled, insert into multiple_violations
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $violations = $_POST['violations'];  // Violations from the form
                $total_amount = $_POST['total'];  // Total amount calculated
                $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
                $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;
                $notes = isset($_POST['notes']) ? $_POST['notes'] : null; // Get notes from the form

                // Update with multiple violations
                $sql_update_multiple = "UPDATE violation SET multiple_violations = ?, multiple_total = ?, notes = ? WHERE first_name = ? AND last_name = ?";
                $stmt_update_multiple = $conn->prepare($sql_update_multiple);
                $stmt_update_multiple->bind_param("sssss", implode(", ", $violations), $total_amount, $notes, $first_name, $last_name);

                if ($stmt_update_multiple->execute()) {
                    header("Location: receipt.php?first_name=" . urlencode($first_name) . "&last_name=" . urlencode($last_name) . "&multiple_total=" . urlencode($total_amount));
                    exit();  // Ensure no further code is executed
                } else {
                    echo "Error: " . $stmt_update_multiple->error;
                }
            }
        }
    }
} else {
    echo "No report found for this ticket number.";
}

// Close connection
$stmt->close();
$conn->close();
?>
