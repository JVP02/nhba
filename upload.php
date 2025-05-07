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
            // Redirect to the same page to prevent form resubmission
            header("Location: upload.php?success=1");
            exit;
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
        <?php include 'comp/sidebar.php'; ?>
        <main class="admin-main">
            <section class="upload-section">
                <h2>Upload Image</h2>
                <div class="upload-card">
                    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                        <p class="success-message">Image uploaded successfully!</p>
                    <?php elseif (isset($errorMessage)): ?>
                        <p class="error-message"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
                    <form id="upload-form" method="POST" action="" enctype="multipart/form-data">
                        <div class="form-row">
                            <input type="text" id="title" name="title" placeholder="Enter Title" required>
                        </div>
                        <div class="form-row">
                            <textarea id="description" name="description" placeholder="Enter Description" required></textarea>
                        </div>
                        <div class="form-row">
                            <input type="file" id="image" name="image" accept="image/*" required>
                        </div>
                        <div class="form-row">
                            <button type="submit" class="upload-button">Upload</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="gallery-section">
                <h2>Gallery</h2>
                <div class="modern-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="list-item">
                            <div class="list-image">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            </div>
                            <div class="list-content">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <small><?php echo htmlspecialchars($row['date']); ?></small>
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