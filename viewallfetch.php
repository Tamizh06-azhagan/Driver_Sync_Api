<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root";  
$password = "";  
$dbname = "driversync";  

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// SQL query to fetch all columns from admindashboard and signup tables
$sql = "SELECT a.*, s.* 
        FROM admindashboard a 
        JOIN signup s ON a.userid = s.id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "No records found"]);
}

// Close connection
$conn->close();
?>
