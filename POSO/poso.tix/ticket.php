<?php
// Start the session
session_start();

// Generate random 8-digit ticket number
$ticket_number = rand(100000, 999999);

// Store the ticket number in the session
$_SESSION['ticket_number'] = $ticket_number;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordinance Infraction Ticket</title> 
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=1.0">
    <style>
        /* FAB styling */
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #dc3545; /* Bootstrap Danger Color */
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            cursor: pointer;
            text-decoration: none;
        }

        .fab:hover {
            background-color: #c82333; /* Darker shade for hover effect */
        }

        .fab i {
            font-size: 24px;
        }
    </style>
</head>
<body>
<script>
    // Automatically set today's date, time, and default city
    document.addEventListener("DOMContentLoaded", function () {
        // Get current date and time
        const today = new Date();
        
        // Format date as yyyy-MM-dd
        const formattedDate = today.toISOString().slice(0, 10);

        // Format time as HH:mm
        const formattedTime = today.toTimeString().slice(0, 5);

        // Set the values to the input fields
        document.getElementById("date").value = formattedDate;
        document.getElementById("time").value = formattedTime;

        // Automatically set city to "Biñan City"
        document.getElementById("city").value = "Biñan City";
    });
</script>
        <div class="container">
        <div class="ticket-container">
            <div class="header-container d-flex justify-content-between align-items-center"> 
                <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
                <div class="col text-center">
                    <p class="title">POSO Traffic Violations</p>
                    <p class="city">-City of Binan, Laguna-</p>
                </div>
                <img src="/POSO/images/arman.png" alt="Right Logo" class="logo">
            </div>
            <br>
            <div class="ticket-info">
                <p class="ticket-label">ORDINANCE INFRACTION TICKET</p>
                <p class="ticket-number">No. <?php echo $ticket_number; ?></p>
            </div>
            <form action="submit_ticket.php" method="POST">
                <input type="hidden" name="ticket_number" value="<?php echo $ticket_number; ?>">

                <!-- Violator's Information Section -->
                <div class="gray">
                    <p>Violator's Information:</p> 
                </div>
                <div class="section">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required class="form-control">
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required class="form-control">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required class="form-control">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required class="form-control">
                    <label for="license">License No.:</label>
                    <input type="text" id="license" name="license" required class="form-control">
                </div>

                <!-- License Confiscation Section -->
                <div class="gray">
                    <p>License Confiscated:</p> 
                </div>
                <div class="radio-container">
                    <label for="confiscated_yes">Yes</label> 
                    <input type="radio" id="confiscated_yes" name="confiscated" value="yes" class="me-2">
                    <label for="confiscated_no">No</label>
                    <input type="radio" id="confiscated_no" name="confiscated" value="no" class="me-2">
                </div>
                <br>

                <!-- Date & Time Section -->
                <div class="gray">
                    <p>Date & Time:</p> 
                </div>
                <div class="section">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" class="form-control">
                    <br>
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" class="form-control">
                </div>

                <!-- Place of Violation Section -->
                <div class="gray">
                    <p>Place of Violation</p> 
                </div>
                <div class="section">
                    <label for="street">Street:</label>
                    <input type="text" id="street" name="street" class="form-control">
                    <label for="plate_number">Plate Number:</label>
                    <input type="text" id="plate_number" name="plate_number" class="form-control">
                    <label for="city">City/Municipality:</label>
                    <input type="text" id="city" name="city" class="form-control">
                    <label for="registration">Registration Number:</label>
                    <input type="text" id="registration" name="registration" class="form-control">
                    <label for="vehicle_type">Vehicle Type:</label>
                    <select id="vehicle_type" name="vehicle_type" class="form-control">
                        <option value="">Select Vehicle Type</option>
                        <option value="Passenger Car">Passenger Car</option>
                        <option value="Motorcycle or Scooter">Motorcycle or Scooter</option>
                        <option value="Public Utility Vehicle">Public Utility Vehicle (PUV)</option>
                        <option value="Truck or Delivery Vehicle">Truck or Delivery Vehicle</option>
                        <option value="Commercial Vehicle">Commercial Vehicle</option>
                        <option value="Emergency Vehicle">Emergency Vehicle</option>
                        <option value="Heavy Equipment">Heavy Equipment Vehicle</option>
                    </select>
                    <label for="vehicle_owner">Vehicle Owner:</label>
                    <input type="text" id="vehicle_owner" name="vehicle_owner" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Next</button>
            </form>
        </div>
    </div>

    <!-- FAB Logout Button -->
    <a href="logout.php" class="fab" title="Logout">
        <i class="fas fa-sign-out-alt "></i>
    </a>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
</body>
</html>