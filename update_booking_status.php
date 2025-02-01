<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "db.php"; // Ensure database connection

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['booking_id']) && isset($_POST['status'])) {
        $booking_id = intval($_POST['booking_id']);
        $status = strtolower(trim($_POST['status']));

        // Check if the provided status is valid
        if ($status === "accepted" || $status === "rejected") {
            $stmt = $conn->prepare("UPDATE bookingdetails SET status = ? WHERE id = ? AND status = 'pending'");
            $stmt->bind_param("si", $status, $booking_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $response['status'] = true;
                $response['message'] = "Booking $status successfully";
            } else {
                $response['status'] = false;
                $response['message'] = "Already updated or invalid booking ID";
            }

            $stmt->close();
        } else {
            $response['status'] = false;
            $response['message'] = "Invalid status. Use 'accepted' or 'rejected'";
        }
    } else {
        $response['status'] = false;
        $response['message'] = "Booking ID and status are required";
    }
} else {
    $response['status'] = false;
    $response['message'] = "Invalid request method";
}

echo json_encode($response);
$conn->close();
?>
