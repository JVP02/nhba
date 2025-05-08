<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$image_id = $_GET['id'] ?? null;

if ($image_id) {
    $query = "SELECT file_path FROM images WHERE id = '$image_id'";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        $filePath = $row['file_path'];

        // Delete the file from the server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the record from the database
        $query = "DELETE FROM images WHERE id = '$image_id'";
        if ($conn->query($query)) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}

die("Failed to delete image.");
?>
