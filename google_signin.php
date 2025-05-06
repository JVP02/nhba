<?php
header("Content-Type: application/json");

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email'], $data['name'])) {
    $email = $conn->real_escape_string($data['email']);
    $name = $conn->real_escape_string($data['name']);

    // Check if the user already exists
    $query = "SELECT * FROM user_requests WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo json_encode(["success" => true, "message" => "User already exists."]);
    } else {
        // Insert new user
        $query = "INSERT INTO user_requests (name, email, password) VALUES ('$name', '$email', '')";
        if ($conn->query($query)) {
            echo json_encode(["success" => true, "message" => "User registered successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to register user."]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
}

$conn->close();
?>
