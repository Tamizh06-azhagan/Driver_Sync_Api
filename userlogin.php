<?php
// Include the database connection file
include('db.php');

// Check if POST request has username and password
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Escape input to prevent SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if user exists
    $sql = "SELECT * FROM signup WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the user details
        $user = $result->fetch_assoc();

        // Compare plain password
        if ($password === $user['password']) {
            // Login successful
            $response = array(
                'status' => 'success',
                'message' => 'Login successful',
                'data' => array(
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'email' => $user['email']
                )
            );
        } else {
            // Invalid password
            $response = array(
                'status' => 'error',
                'message' => 'Invalid password'
            );
        }
    } else {
        // User not found
        $response = array(
            'status' => 'error',
            'message' => 'Invalid username'
        );
    }

    // Send the response as JSON
    echo json_encode($response);
} else {
    // Missing username or password
    $response = array(
        'status' => 'error',
        'message' => 'Username and password are required'
    );
    echo json_encode($response);
}

$conn->close();
?>
