<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Ensure the correct path to db_connect.php
include __DIR__ . "/db.php";

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);

        if ($stmt = $conn->prepare("SELECT name, username,image_path FROM signup WHERE id = ?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $response['status'] = true;
                $response['message'] = "User found";
                $response['data'] = $user;
            } else {
                $response['status'] = false;
                $response['message'] = "No user found";
            }

            $stmt->close();
        } else {
            $response['status'] = false;
            $response['message'] = "Database query failed";
        }
    } else {
        $response['status'] = false;
        $response['message'] = "User ID is required";
    }
} else {
    $response['status'] = false;
    $response['message'] = "Invalid request method";
}

echo json_encode($response);
$conn->close();
?>
