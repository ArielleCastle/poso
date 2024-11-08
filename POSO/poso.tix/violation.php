<?php
// Start session
session_start();
// Include the database connection file
include 'connection.php'; // Ensure the path is correct

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
    $violations = $_POST['violations'];
    $total_amount = $_POST['total'];
    $others_total = isset($_POST['others_total']) ? $_POST['others_total'] : 0;
    $total_amount += $others_total;

    $others_violation = isset($_POST['others_violation']) ? $_POST['others_violation'] : null;

    $sql_insert = "INSERT INTO violation (ticket_number, first_name, last_name, violations, total_amount, others_violation) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("isssss", $ticket_number, $first_name, $last_name, implode(", ", $violations), $total_amount, $others_violation);

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
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <link rel="stylesheet" href="style.css?v=1.0">
    <script>
        function calculateTotal() {
            let checkboxes = document.querySelectorAll('input[name="violations[]"]:checked');
            let total = 0;
            let othersSelected = false;

            checkboxes.forEach((checkbox) => {
                if (checkbox.value.startsWith("OTHERS")) {
                    othersSelected = true;
                } else {
                    total += parseFloat(checkbox.getAttribute('data-price'));
                }
            });

            let totalField = document.getElementById('total');
            let othersTotalField = document.getElementById('othersTotal');

            if (othersSelected && checkboxes.length === 1) {
                totalField.removeAttribute("readonly");
                totalField.value = '';
                othersTotalField.style.display = "none";
            } else if (othersSelected && checkboxes.length > 1) {
                totalField.setAttribute("readonly", true);
                totalField.value = total.toFixed(2);
                othersTotalField.style.display = "block";
            } else {
                totalField.setAttribute("readonly", true);
                totalField.value = total.toFixed(2);
                othersTotalField.style.display = "none";
            }
        }

        function toggleOthersField() {
            let othersViolationField = document.getElementById('othersViolation');
            let othersCheckbox = document.getElementById('others-checkbox');
            othersViolationField.style.display = othersCheckbox.checked ? "block" : "none";
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            let checkboxes = document.querySelectorAll('input[name="violations[]"]');
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', calculateTotal);
            });
        });
    </script>
</head>
<body>
    <div class="ticket-container">
        <div class="header-container">
            <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
            <div class="col text-center">
                <p class="title">POSO Traffic Violations</p>
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
            <input type="hidden" name="ticket_number" value="<?php echo $ticket_number; ?>">

            <div class="gray1">
                <p>You are hereby cited for committing the traffic violations:</p>
            </div>
            <div class="section">
                <input type="checkbox" name="violations[]" value="FAILURE TO WEAR HELMET - 200" data-price="200"> FAILURE TO WEAR HELMET <br>
                <input type="checkbox" name="violations[]" value="OPEN MUFFLER/NUISANCE - 1000" data-price="1000"> OPEN MUFFLER/NUISANCE <br>
                <input type="checkbox" name="violations[]" value="ARROGANT - 1000" data-price="1000"> ARROGANT<br>
                <input type="checkbox" name="violations[]" value="ONEWAY - 200" data-price="200"> ONEWAY<br>
                <input type="checkbox" name="violations[]" value="DRIVING WITHOUT LICENSE/INVALID LICENSE - 1000" data-price="1000"> DRIVING WITHOUT LICENSE/INVALID LICENSE<br>
                <input type="checkbox" name="violations[]" value="NO OR/CR WHILE DRIVING - 500" data-price="500"> NO OR/CR WHILE DRIVING<br>
                <input type="checkbox" name="violations[]" value="UNREGISTERED MOTOR VEHICLE - 500" data-price="500"> UNREGISTERED MOTOR VEHICLE<br>
                <input type="checkbox" name="violations[]" value="OBSTRUCTION - 500" data-price="500"> OBSTRUCTION<br>
                <input type="checkbox" name="violations[]" value="DISREGARDING TRAFFIC SIGNS - 100" data-price="100"> DISREGARDING TRAFFIC SIGNS<br>
                <input type="checkbox" name="violations[]" value="DISREGARDING TRAFFIC OFFICER - 100" data-price="100"> DISREGARDING TRAFFIC OFFICER<br>
                <input type="checkbox" name="violations[]" value="TRUCK BAN - 100" data-price="100"> TRUCK BAN<br>
                <input type="checkbox" name="violations[]" value="STALLED VEHICLE - 100" data-price="100"> STALLED VEHICLE<br>
                <input type="checkbox" name="violations[]" value="RECKLESS DRIVING - 100" data-price="100"> RECKLESS DRIVING<br>
                <input type="checkbox" name="violations[]" value="DRIVING UNDER THE INFLUENCE OF LIQUOR - 100" data-price="100"> DRIVING UNDER THE INFLUENCE OF LIQUOR<br>
                <input type="checkbox" name="violations[]" value="INVALID OR NO FRANCHISE/COLORUM - 100" data-price="100"> INVALID OR NO FRANCHISE/COLORUM<br>
                <input type="checkbox" name="violations[]" value="OPERATING OUT OF LINE - 100" data-price="100"> OPERATING OUT OF LINE<br>
                <input type="checkbox" name="violations[]" value="TRIP - CUTTING - 100" data-price="100"> TRIP - CUTTING<br>
                <input type="checkbox" name="violations[]" value="OVERLOADING - 100" data-price="100"> OVERLOADING<br>
                <input type="checkbox" name="violations[]" value="LOADING/UNLOADING IN PROHIBITED ZONE - 100" data-price="100"> LOADING/UNLOADING IN PROHIBITED ZONE<br>
                <input type="checkbox" name="violations[]" value="INVOLVE IN ACCIDENT - 100" data-price="100"> INVOLVE IN ACCIDENT<br>
                <input type="checkbox" name="violations[]" value="SMOKE BELCHING - 100" data-price="100"> SMOKE BELCHING<br>
                <input type="checkbox" name="violations[]" value="NO SIDE MIRROR - 100" data-price="100"> NO SIDE MIRROR<br>
                <input type="checkbox" name="violations[]" value="JAY WALKING - 100" data-price="100"> JAY WALKING<br>
                <input type="checkbox" name="violations[]" value="WEARING SLIPPERS/SHORTS/SANDO - 100" data-price="100"> WEARING SLIPPERS/SHORTS/SANDO<br>
                <input type="checkbox" name="violations[]" value="ILLEGAL VENDING - 100" data-price="100"> ILLEGAL VENDING<br>
                <input type="checkbox" name="violations[]" value="IMPOUNDED - 100" data-price="100"> IMPOUNDED<br>
                <input type="checkbox" name="violations[]" value="OTHERS" id="others-checkbox" onclick="toggleOthersField()"> OTHERS<br>
                <div class="section" id="othersViolation" style="display:none;">
                    <label for="others_violation">Describe OTHERS Violation:</label>
                    <input type="text" id="others_violation" name="others_violation" placeholder="Specify others violation">
                </div>
            </div>

            <div class="gray">
                <p>Total Amount:</p>
            </div>
            <div class="section">
                <label for="total">Total:</label>
                <input type="text" id="total" name="total" value="0.00" readonly>
            </div>

            <div class="section" id="othersTotal" style="display:none;">
                <label for="others_total">Total for OTHERS:</label>
                <input type="text" id="others_total" name="others_total" value="0.00">
            </div>

            <div class="gray">
                <p>Notes:</p>
            </div>
            <div class="section">
                <label for="notes">Notes:</label>
                <input type="text" id="notes" name="notes" value="">
            </div>

            <div class="section">
                <button type="submit">See Breakdown of Violation</button>
            </div>
        </form>
    </div>
</body>
</html>
