<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "db.php"; // Ensure database connection

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['userid'])) {
        $userid = intval($_POST['userid']);

        // Fetch booking details and join with signup table to get username
        $stmt = $conn->prepare("
            SELECT b.id, b.userid, s.username, b.dateofbooking, b.status 
            FROM bookingdetails b 
            INNER JOIN signup s ON b.userid = s.id 
            WHERE b.driver_id = ?
        ");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $bookings = array();
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            $response['status'] = true;
            $response['message'] = "Bookings fetched successfully";
            $response['data'] = $bookings;
        } else {
            $response['status'] = false;
            $response['message'] = "No bookings found for this user";
        }

        $stmt->close();
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
