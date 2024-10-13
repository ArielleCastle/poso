<?php
// Start the session
session_start();

// Generate random 8-digit ticket number
$ticket_number = rand(10000000, 99999999);

// Store the ticket number in the session
$_SESSION['ticket_number'] = $ticket_number;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordinance Infraction Ticket</title>
    <!-- Cache-busting by adding a version query string -->
    <link rel="stylesheet" href="style.css?v=1.0">
</head>
<body>

<div class="ticket-container">
    <div class="header-container"> 
        <!-- Ensure correct image paths relative to the PHP file -->
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

    <form action="submit_ticket.php" method="POST">
        <!-- Hidden input field to pass the ticket number -->
        <input type="hidden" name="ticket_number" value="<?php echo $ticket_number; ?>">

        <!-- Violator's Information Section -->
        <div class="gray"> 
            <p>Violator's Information:</p> 
        </div>
        <div class="section">
            <!-- Adjust background image path -->
            <img class="bg" src="/POSO/images/poso.png" alt="Background Image">

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="license">License No.:</label>
            <input type="text" id="license" name="license" required>
        </div>

        <!-- License Confiscation Section -->
        <div class="gray">
          <p>License Confiscated:</p> 
        </div>
       <!-- Add the 'radio-group' class here -->
       <div class="section radio-group">
           <label for="confiscated_yes">Yes</label> 
           <input type="radio" id="confiscated_yes" name="confiscated" value="yes">
           <label for="confiscated_no">No</label>
           <input type="radio" id="confiscated_no" name="confiscated" value="no">
       </div>


        <!-- Date & Time Section -->
        <div class="gray">
            <p>Date & Time:</p> 
        </div>
        <div class="section">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date">

            <label for="time">Time:</label>
            <input type="time" id="time" name="time">
        </div>

        <!-- Place of Violation Section -->
        <div class="gray">
            <p>Place of Violation</p> 
        </div>
        <div class="section">
            <label for="street">Street:</label>
            <input type="text" id="street" name="street">

            <label for="plate_number">Plate Number:</label>
            <input type="text" id="plate_number" name="plate_number">

            <label for="city">City/Municipality:</label>
            <input type="text" id="city" name="city">

            <label for="registration">Registration Number:</label>
            <input type="text" id="registration" name="registration">

            <label for="vehicle_type">Vehicle Type:</label>
            <input type="text" id="vehicle_type" name="vehicle_type">

            <label for="vehicle_owner">Vehicle Owner:</label>
            <input type="text" id="vehicle_owner" name="vehicle_owner">
        </div>

        <button type="submit">Next</button>
    </form>
</div>

</body>
</html>
