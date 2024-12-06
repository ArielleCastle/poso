<?php
// Start the session
session_start();

// Get the ticket number from the URL (if it's available)
$ticket_number = isset($_GET['ticket_number']) ? $_GET['ticket_number'] : 'N/A'; // Default to 'N/A' if not set

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Signature Picker</title>
    <!-- Link to external CSS file -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">  
    <style>
        .logo {
            width: 50px;
            height: auto;
        }

        .ticket-info {
            display: flex;
            justify-content: space-between;
        }

        .ticket-label {
            font-weight: bold;
            color: #333;
        }

        .ticket-number {
            font-weight: bold;
            color: red;
        }

        .signature-section {
            margin-top: 30px;
            text-align: center;
        }

        .canvas-container {
            margin-top: 10px;
            text-align: center;
            border: 2px solid #ccc; /* Bordered canvas */
            padding: 20px;
            width: 300px;
            height: 150px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-container">
            <div class="header-container">
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

            <div class="signature-section">
                <h3>OFFICER'S SIGNATURE</h3>
                <select id="officerSignature" onchange="updateSignature('signatureCanvas', 'officerSignature')">
                    <option value="">Select Officer</option>
                    <?php
                    // Fetch officer signatures from the database
                    $conn = new mysqli("localhost", "root", "", "poso");

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Query to fetch last names and signatures
                    $sql = "SELECT lastname, signature FROM login WHERE signature IS NOT NULL";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="data:image/jpeg;base64,' . base64_encode($row['signature']) . '">' . htmlspecialchars($row['lastname']) . '</option>';
                        }
                    } else {
                        echo '<option value="">No officers found</option>';
                    }

                    $conn->close();
                    ?>
                </select>

                <!-- Canvas for displaying the signature -->
                <div class="canvas-container" id="signatureCanvas">
                    <!-- Signature will be displayed here as an image -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to update the signature display
        function updateSignature(canvasId, selectId) {
            const canvasContainer = document.getElementById(canvasId);
            const selectElement = document.getElementById(selectId);
            const selectedValue = selectElement.value;

            // Clear the existing content
            canvasContainer.innerHTML = "";

            // If a signature is selected, display it
            if (selectedValue) {
                const img = document.createElement("img");
                img.src = selectedValue; // Base64 image data
                canvasContainer.appendChild(img);
            }
        }
    </script>
</body>
</html>
