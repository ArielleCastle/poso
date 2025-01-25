<?php
// Include database connection
include 'connection.php'; // Ensure this file sets up the $conn variable

// Get JSON input from the AJAX request
$data = json_decode(file_get_contents("php://input"), true);

// Extract the input fields
$first_name = $data['first_name'] ?? '';
$middle_name = $data['middle_name'] ?? '';
$last_name = $data['last_name'] ?? '';

$response = ['exists' => false];

if ($first_name && $middle_name && $last_name) {
    // Query the database for the record
    $stmt = $conn->prepare("SELECT dob, address, license, registration FROM report WHERE first_name = :first_name AND middle_name = :middle_name AND last_name = :last_name LIMIT 1");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':middle_name', $middle_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['exists'] = true;
        $response['dob'] = $record['dob'];
        $response['address'] = $record['address'];
        $response['license'] = $record['license'];
        $response['registration'] = $record['registration'];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
