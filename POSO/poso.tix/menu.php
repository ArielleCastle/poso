<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POSO Main Menu</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=1.0">
    <style>
        .ticket-container {
            width: 500px;
            height: 600px;
        }

        .square-button {
            width: 150px;
            height: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-radius: 20px; /* Adds rounded corners */
            font-size: 16px;
            background-color: transparent; /* Transparent background */
            border: 2px solid #007bff; /* Blue border */
            color: #007bff; /* Blue text */
            font-weight: bold;
            transition: all 0.3s ease; /* Smooth transition for hover effects */
        }

        .square-button:hover {
            background-color: rgba(0, 123, 255, 0.1); /* Slight blue background on hover */
            color: #0056b3; /* Darker blue text on hover */
            border-color: #0056b3; /* Darker blue border on hover */
        }

        .btn-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column; /* Stack buttons vertically */
            justify-content: center; /* Center buttons vertically */
            align-items: center; /* Center buttons horizontally */
            gap: 20px; /* Adds space between the buttons */
            height: 80%; /* Takes full height of the container */
        }

        .container {
            height: 100vh; /* Full viewport height */
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="ticket-container d-flex flex-column justify-content-center align-items-center">
            <div class="header-container d-flex justify-content-between align-items-center"> 
                <img src="/POSO/images/left.png" alt="Left Logo" class="logo">
                <div class="col text-center">
                    <p class="title">POSO Traffic Violations</p>
                    <p class="city">-City of Binan, Laguna-</p>
                </div>
                <img src="/POSO/images/arman.png" alt="Right Logo" class="logo">
            </div>
            
            <!-- Button Container -->
            <div class="btn-container">
                <button class="btn btn-primary square-button" onclick="location.href='ticket.php'">CREATE ORDINANCE INFRACTION TICKET</button>

                <button class="btn btn-secondary square-button" onclick="location.href='logout.php'">LOGOUT</button>
                <button class="btn btn-primary square-button" onclick="location.href='history.php'">TICKET HISTORY</button>
            </div>
        </div>
    </div>
</body>
</html>
