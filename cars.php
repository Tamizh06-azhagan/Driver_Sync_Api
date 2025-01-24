<?php
// Include database connection
include('db.php');

// Set the header for JSON response
header('Content-Type: application/json');

// Check if required fields are provided
if (isset($_POST['userid'], $_POST['car_name'], $_POST['image_path'], $_POST['condition'])) {
    // Sanitize inputs
    $userid = intval($_POST['userid']);
    $car_name = htmlspecialchars(strip_tags($_POST['car_name']));
    $image_path = htmlspecialchars(strip_tags($_POST['image_path']));
    $condition = htmlspecialchars(strip_tags($_POST['condition']));

    // Validate condition
    $allowed_conditions = ['New', 'Used'];
    if (!in_array($condition, $allowed_conditions)) {
        echo json_encode(['status' => false, 'message' => 'Invalid condition (must be New or Used).']);
        exit();
    }

    // Insert into the database
    $query = "INSERT INTO cars (userid, car_name, image_path, condition) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $userid, $car_name, $image_path, $condition);

    if ($stmt->execute()) {
        echo json_encode(['status' => true, 'message' => 'Car added successfully.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to add car.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => false, 'message' => 'Missing required fields.']);
}

$conn->close();
?>
