<?php

// Include the database connection file
include 'db.php';

// Function to insert a car
function insertCar($conn, $userid, $car_name, $image_path, $condition) {
    // Check for duplicates
    $checkQuery = "SELECT COUNT(*) FROM cars WHERE userid = ? AND car_name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('is', $userid, $car_name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return ['status' => false, 'message' => 'Duplicate car entry.'];
    }

    // Insert new car
    $insertQuery = "INSERT INTO cars (userid, car_name, image_path, `condition`) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    try {
        $stmt->bind_param('isss', $userid, $car_name, $image_path, $condition);
        $stmt->execute();
        $stmt->close();
        return ['status' => true, 'message' => 'Car inserted successfully.'];
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Failed to insert car: ' . $e->getMessage()];
    }
}

// Handle API request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['userid'], $data['car_name'], $data['image_path'], $data['condition'])) {
        $response = insertCar(
            $conn,
            $data['userid'],
            $data['car_name'],
            $data['image_path'],
            $data['condition']
        );
    } else {
        $response = ['status' => false, 'message' => 'Invalid input data.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
}

?>
