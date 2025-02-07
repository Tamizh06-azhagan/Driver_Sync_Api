<?php
// Database connection
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";  // Your MySQL password
$dbname = "driversync";  // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$userid = $_POST['userid'];
$driver_id = $_POST['driver_id'];
$dateofbooking = $_POST['dateofbooking'];
$status = $_POST['status'];
$pickup = $_POST['pickup'];
$drop_location = $_POST['drop'];  // Renaming variable to avoid SQL conflicts

// Check for existing booking
$sql_check = "SELECT * FROM bookingdetails 
              WHERE userid = '$userid' 
              AND driver_id = '$driver_id' 
              AND dateofbooking = '$dateofbooking'";

$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Duplicate found
    echo json_encode(["status" => "error", "message" => "Booking already exists for this user, driver, and date"]);
} else {
    // No duplicate, proceed with insertion
    $sql_insert = "INSERT INTO bookingdetails (`userid`, `driver_id`, `dateofbooking`, `status`, `pickup`, `drop_location`) 
                   VALUES ('$userid', '$driver_id', '$dateofbooking', '$status', '$pickup', '$drop_location')";

    if ($conn->query($sql_insert) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Booking details inserted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

// Close connection
$conn->close();
?>
