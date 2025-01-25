<?php
// Include database connection file
include_once 'db.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input values from the form
    $userid = isset($_POST['userid']) ? htmlspecialchars(strip_tags($_POST['userid'])) : '';
    $age = isset($_POST['age']) ? htmlspecialchars(strip_tags($_POST['age'])) : '';
    $experience_years = isset($_POST['experience_years']) ? htmlspecialchars(strip_tags($_POST['experience_years'])) : '';
    $contact_number = isset($_POST['contact_number']) ? htmlspecialchars(strip_tags($_POST['contact_number'])) : '';

    // Validate required fields
    if (empty($userid) || empty($age) || empty($experience_years) || empty($contact_number)) {
        echo json_encode(['status' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Insert data into the database
    $query = "INSERT INTO driverinfo (userid, age, experienceyears, contactnumber) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $userid, $age, $experience_years, $contact_number);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => true,
            'message' => 'Driver details added successfully!',
            'userid' => $userid
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Error: ' . $stmt->error
        ]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
