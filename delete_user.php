<?php
header("Content-Type: application/json");

session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Validate input
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete the user from the `users` table
    $query = "DELETE FROM users WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(["success" => true, "message" => "User deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete user."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
}

$conn->close();
?>
