<?php

// Include the database connection file
include 'db.php';

// Set upload directory
$uploadDir = "uploads/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if required fields are set
    if (isset($_POST['userid'], $_POST['car_name'], $_POST['condition']) && isset($_FILES['image'])) {
        
        $userid = intval($_POST['userid']);
        $car_name = trim($_POST['car_name']);
        $condition = trim($_POST['condition']);

        // Handle image upload
        $image = $_FILES['image'];
        $imageName = basename($image['name']);
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = "car_" . time() . "_" . uniqid() . "." . $imageExtension;
        $imagePath = $uploadDir . $newImageName;

        // Validate file type (only allow images)
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($imageExtension), $allowedTypes)) {
            echo json_encode(['status' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.']);
            exit;
        }

        // Check for duplicate car entry
        $checkQuery = "SELECT COUNT(*) FROM cars WHERE userid = ? AND car_name = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('is', $userid, $car_name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo json_encode(['status' => false, 'message' => 'Duplicate car entry.']);
            exit;
        }

        // Move uploaded file
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            // Insert into database
            $insertQuery = "INSERT INTO cars (userid, car_name, image_path, `condition`) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);

            if ($stmt) {
                $stmt->bind_param('isss', $userid, $car_name, $imagePath, $condition);
                if ($stmt->execute()) {
                    echo json_encode(['status' => true, 'message' => 'Car inserted successfully.', 'image_url' => $imagePath]);
                } else {
                    echo json_encode(['status' => false, 'message' => 'Database insert failed.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => false, 'message' => 'Failed to prepare statement.']);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'Image upload failed.']);
        }

    } else {
        echo json_encode(['status' => false, 'message' => 'Invalid input data.']);
    }

} else {
    echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
}

?>
