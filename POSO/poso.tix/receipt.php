<?php
// Start session
session_start();

// Include the database connection file
include 'connection.php'; // Ensure the path is correct

// Get the ticket number from the session
$ticket_number = $_SESSION['ticket_number'];

// Get the first name, last name, and total from the URL
$first_name = $_GET['first_name'];
$last_name = $_GET['last_name'];
$total_amount = $_GET['total'];

// Fetch the violation record for the given ticket number
$sql = "SELECT 'First Violation' AS violation_type, first_violation, first_total, notes FROM violation WHERE ticket_number = ?
        UNION ALL
        SELECT 'Second Violation' AS violation_type, second_violation, second_total, notes FROM 2_violation WHERE ticket_number = ?
        UNION ALL
        SELECT 'Third Violation' AS violation_type, third_violation, third_total, notes FROM 3_violation WHERE ticket_number = ?
        ORDER BY violation_type ASC"; // Order the results by violation type

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $ticket_number, $ticket_number, $ticket_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Violation Receipt</title>
        <link rel="stylesheet" href="style.css?v=1.0"> <!-- Link to the stylesheet -->
        <style>
            .impound-warning {
                color: red;
                text-align: center;
                font-weight: bold;
                font-size: 1.2em;
                margin-top: 10px;
            }
        </style>
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
                    <p class="ticket-number">No. <?php echo htmlspecialchars($ticket_number); ?></p> <!-- Display the ticket number -->
                </div>
                <div class="gray">

                <h3>BREAKDOWN OF VIOLATION CHARGES</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>VIOLATION NUMBER</th>
                            <th>VIOLATION</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($violation = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($violation['violation_type']) . "</td>
                                <td>" . htmlspecialchars($violation['first_violation'] ?? $violation['second_violation'] ?? $violation['third_violation']) . "</td>
                                <td>" . htmlspecialchars($violation['first_total'] ?? $violation['second_total'] ?? $violation['third_total']) . "</td>
                            </tr>";

                            // Display impound warning if this is the third violation
                            if ($violation['violation_type'] === 'Third Violation') {
                                echo "<tr><td colspan='3' class='impound-warning'>THIS VIOLATOR IS SUBJECT FOR VEHICLE IMPOUND.</td></tr>";
                            }
                        }

                        if ($result->num_rows == 0) {
                            echo "<tr><td colspan='3'>No violations found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

               
<br>
                <h4>NOTES:</h4>
                <ul>
                    <?php
                    // Reset the result pointer to fetch notes
                    $result->data_seek(0); // Move to the first record
                    while ($violation = $result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($violation['notes']) . "</li>";
                    }
                    ?>
                </ul>

                <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($total_amount); ?></p>
                <p>Thank you for your compliance.</p>
                
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "<p>No violation records found for this individual.</p>";
}

// Close connection
$stmt->close();
$conn->close();
?>
