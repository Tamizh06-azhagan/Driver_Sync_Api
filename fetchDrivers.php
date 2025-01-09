<?php
header("Content-Type: application/json"); // Set the response type to JSON

include_once 'db.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // SQL query to join driverinfo and signup tables
    $query = "
        SELECT 
            signup.id AS signup_id, 
            signup.name, 
            signup.username, 
            signup.email, 
            signup.role, 
            driverinfo.id AS driver_id, 
            driverinfo.age, 
            driverinfo.experienceyears, 
            driverinfo.contactnumber 
        FROM 
            driverinfo
        JOIN 
            signup 
        ON 
            driverinfo.userid = signup.id
    ";

    $result = $conn->query($query);

    // Check if the query was successful and if results were returned
    if ($result->num_rows > 0) {
        $drivers = [];

        // Fetch all rows and store them in an array
        while($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }

        // Send a JSON response with the fetched drivers
        echo json_encode([
            "status" => "success",
            "drivers" => $drivers
        ]);
    } else {
        // If no results, send an empty array response
        echo json_encode([
            "status" => "success",
            "drivers" => []
        ]);
    }
} else {
    // If the request method is not GET, send an error response
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}

// Close the database connection
$conn->close();
?>
