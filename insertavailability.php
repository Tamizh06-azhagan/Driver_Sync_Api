<?php
// Include the database connection file
include_once 'db.php';

// Set the header to return JSON response
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request method. Please use POST.'
    ]);
    exit();
}

// Validate required fields
if (!isset($_POST['userid']) || !isset($_POST['availability']) || !isset($_POST['availability_date'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Missing required fields: userid, availability, or availability_date.'
    ]);
    exit();
}

// Sanitize and retrieve input data
$userid = htmlspecialchars(strip_tags($_POST['userid']));
$availability = htmlspecialchars(strip_tags($_POST['availability']));
$availability_date = htmlspecialchars(strip_tags($_POST['availability_date']));

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $availability_date)) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid date format. Expected format: YYYY-MM-DD.'
    ]);
    exit();
}

// Prepare the SQL query to insert data
$query = "INSERT INTO admindashboard (userid, availability, availability_date) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("iss", $userid, $availability, $availability_date);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode([
            'status' => true,
            'message' => 'Data inserted successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Error inserting data into the database.'
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Failed to prepare the SQL statement.'
    ]);
}

// Close the database connection
$conn->close();
?>
