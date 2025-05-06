<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $uploadDir = '../asset/gallery/community/';

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory with write permissions
    }

    $uploadFile = $uploadDir . basename($_FILES['image']['name']);

    // Move uploaded file to the server
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        // Store file path and metadata in the `community_update` table
        $query = "INSERT INTO community_update (title, description, image_url, date) VALUES ('$title', '$description', '$uploadFile', NOW())";
        if ($conn->query($query)) {
            $successMessage = "Image uploaded and saved successfully!";
        } else {
            $errorMessage = "Failed to save image to the database: " . $conn->error;
        }
    } else {
        $errorMessage = "Failed to upload image. Please check the directory permissions.";
    }
}

// Fetch updates from the database
$query = "SELECT * FROM community_update ORDER BY date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Upload</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="upload.php" class="active">Upload</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="settings.php">Settings</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <section class="upload-section">
                <h2>Upload Image</h2>
                <div class="upload-card">
                    <?php if (isset($successMessage)): ?>
                        <p class="success-message"><?php echo $successMessage; ?></p>
                    <?php elseif (isset($errorMessage)): ?>
                        <p class="error-message"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
                    <form id="upload-form" method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Select Image</label>
                            <input type="file" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="upload-button">Upload</button>
                    </form>
                </div>
            </section>

            <section class="gallery-section">
                <h2>Gallery</h2>
                <div id="admin-gallery" class="gallery-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="gallery-card">
                            <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['title']; ?>">
                            <div class="gallery-card-content">
                                <h3><?php echo $row['title']; ?></h3>
                                <p><?php echo $row['description']; ?></p>
                                <small><?php echo $row['date']; ?></small>
                            </div>
                            <div class="gallery-card-actions">
                                <button class="edit-button" onclick="editImage(<?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-button" onclick="deleteImage(<?php echo $row['id']; ?>)">Delete</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
    <script>
        function editImage(id) {
            alert('Edit functionality for ID: ' + id);
            // Add logic to handle editing
        }

        function deleteImage(id) {
            if (confirm('Are you sure you want to delete this image?')) {
                window.location.href = 'delete_image.php?id=' + id;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>