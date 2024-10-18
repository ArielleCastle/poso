<?php
// Start session
session_start();

// Include the database connection file
include 'connection.php'; // Ensure the path is correct

}

// Get the ticket number from the session
$ticket_number = $_SESSION['ticket_number'];

// Get the first name and last name from the URL
$first_name = $_GET['first_name'];  // Pass these values in the URL from the previous page
$last_name = $_GET['last_name']; 

// Fetch violations associated with the first name and last name
$sql = "SELECT first_violation, first_total, notes FROM violation WHERE first_name = ? AND last_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $first_name, $last_name);
$stmt->execute();
$result = $stmt->get_result();

// Initialize total amount and violations array
$total_amount = 0;
$violations = [];
$notes = '';  // Initialize notes variable here

// Fetch violations and calculate total
while ($row = $result->fetch_assoc()) {
    // Push each violation and its amount to the violations array
    $violations[] = [
        'violation' => $row['first_violation'],
        'total' => (int)$row['first_total']  // Store total as an integer
    ];
    // Store notes if available
    $notes = $row['notes'] ? $row['notes'] : '';  // Capture the notes from the row, default to an empty string
    // Sum up the total for display
    $total_amount += (int)$row['first_total'];  
}

// HTML receipt layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>CITY OF BIÑAN</h2>
    <p>BIÑAN CITY, LAGUNA</p>
    <p>ORDINANCE INFRACTION TICKET:</p>
    <p>NO. <?php echo htmlspecialchars($ticket_number); ?></p>
    
        
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

</body>
</html>

<?php
// Close connection
$stmt->close();
$conn->close();
?>
