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

// Fetch violations associated with the first name and last name
$sql = "SELECT first_violation, first_total, second_violation, second_total, third_violation, third_total, multiple_violations, multiple_total, others_violation, others_total, notes 
        FROM violation 
        WHERE first_name = ? AND last_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $first_name, $last_name);
$stmt->execute();
$result = $stmt->get_result();

// Initialize total amount and violations array
$total_amount = 0;
$violations = [];
$notes = '';  // Initialize notes variable here

// Fetch violations and calculate total
if ($row = $result->fetch_assoc()) {
    // Check for different types of violations
    if (!empty($row['first_violation'])) {
        // Add first violation
        $violations[] = [
            'violation' => $row['first_violation'],
            'total' => (int)$row['first_total']
        ];
        $total_amount += (int)$row['first_total'];
    }
    
    // Add others violation only once
    if (!empty($row['others_violation']) && !in_array($row['others_violation'], array_column($violations, 'violation'))) {
        // Add others violation
        $violations[] = [
            'violation' => $row['others_violation'],
            'total' => (int)$row['others_total']
        ];
        $total_amount += (int)$row['others_total'];
    }

    // Check for second violation
    if (!empty($row['second_violation'])) {
        $violations[] = [
            'violation' => $row['second_violation'],
            'total' => (int)$row['second_total']
        ];
        $total_amount += (int)$row['second_total'];
    }

    // Check for third violation
    if (!empty($row['third_violation'])) {
        $violations[] = [
            'violation' => $row['third_violation'],
            'total' => (int)$row['third_total']
        ];
        $total_amount += (int)$row['third_total'];
    }

    // Check for multiple violations
    if (!empty($row['multiple_violations'])) {
        $violations[] = [
            'violation' => $row['multiple_violations'],
            'total' => (int)$row['multiple_total']
        ];
        $total_amount += (int)$row['multiple_total'];
    }

    // Capture the notes
    $notes = $row['notes'] ? $row['notes'] : '';  // Capture the notes from the row, default to an empty string
}

// HTML receipt layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation Receipt</title>
    <link rel="stylesheet" href="style.css?"> <!-- Link to the stylesheet -->
</head>
<body>
    <div class="container">
    <div class="ticket-container">
        <div class="header-container d-flex justify-content-between align-items-center"> 
            <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
          
            <div class="col text-center">
                <p class="title">Traffic Violations</p>
                <p class="city">-City of Binan, Laguna-</p>
            </div>

            <img src="/POSO/images/arman.png" alt="Right Logo" class="logo">
        </div>

        <div class="ticket-info">
            <p class="ticket-label">Ordinance Infraction Ticket</p>
            <p class="ticket-number">No. <?php echo $ticket_number; ?></p>
        </div>

                
        
        <h3>BREAKDOWN OF VIOLATION CHARGES</h3>
        
        <table>
            <thead>
                <tr>
                    <th>VIOLATION</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($violations as $violation): ?>
                <tr>
                    <td><?php echo htmlspecialchars($violation['violation']); ?></td>
                    <td><?php echo htmlspecialchars($violation['total']); ?></td> <!-- Display the amount for each violation -->
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="total">TOTAL:</td>
                    <td class="total"><?php echo htmlspecialchars($total_amount); ?></td>
                </tr>
            </tbody>
        </table>
        
        <?php if ($notes): // Check if there are any notes ?>
            <h4>Additional Notes:</h4>
            <p><?php echo nl2br(htmlspecialchars($notes)); ?></p> <!-- Display notes, converting new lines to <br> -->
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close connection
$stmt->close();
$conn->close();
?>
