<?php
// Include database connection file
include_once 'db.php';

// Set response type to JSON
header('Content-Type: application/json');
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input values from the form
    $age = isset($_POST['age']) ? htmlspecialchars(strip_tags($_POST['age'])) : '';
    $experience years = isset($_POST['experience years']) ? htmlspecialchars(strip_tags($_POST['experience years'])) : '';
    $contact number = isset($_POST['contact number']) ? htmlspecialchars(strip_tags($_POST['contact number'])) : '';

    // Validate required fields
    if (empty($age) || empty($experience years) || empty($contact number)) {
        echo json_encode(['status' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Insert data into the database
    $query = "INSERT INTO driverinfo (age, experienceyears, contactnumber) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $age, $experience years, $contactnumber);

    if ($stmt->execute()) {
        echo json_encode(['status' => true, 'message' => 'Driver details added successfully!']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
