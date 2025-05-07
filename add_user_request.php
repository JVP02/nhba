<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && isset($_GET['role'])) {
    $id = intval($_GET['id']);
    $role = $conn->real_escape_string($_GET['role']);

    // Fetch the user request details
    $query = "SELECT * FROM user_requests WHERE id = $id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $userRequest = $result->fetch_assoc();
        $name = $conn->real_escape_string($userRequest['name']);
        $email = $conn->real_escape_string($userRequest['email']);

        // Insert the user into the `users` table
        $insertQuery = "INSERT INTO users (name, email, role) VALUES ('$name', '$email', '$role')";
        if ($conn->query($insertQuery)) {
            // Delete the user request from the `user_requests` table
            $deleteQuery = "DELETE FROM user_requests WHERE id = $id";
            if ($conn->query($deleteQuery)) {
                header("Location: users.php?success=1");
                exit;
            } else {
                // Log the error if the DELETE query fails
                error_log("Error deleting user request: " . $conn->error);
                header("Location: users.php?error=Failed to delete user request.");
                exit;
            }
        } else {
            // Log the error if the INSERT query fails
            error_log("Error inserting user: " . $conn->error);
            header("Location: users.php?error=Failed to add user.");
            exit;
        }
    } else {
        header("Location: users.php?error=User request not found.");
        exit;
    }
} else {
    header("Location: users.php?error=Invalid request.");
    exit;
}

$conn->close();
?>
