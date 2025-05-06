<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch the image file path
    $query = "SELECT image_url FROM community_update WHERE id = $id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image_url'];

        // Delete the image file from the server
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete the record from the `community_update` table
        $query = "DELETE FROM community_update WHERE id = $id";
        if ($conn->query($query)) {
            header("Location: upload.php?message=Image deleted successfully");
            exit;
        } else {
            echo "Error deleting record from community_update table: " . $conn->error;
        }
    } else {
        echo "Image not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
