<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$upload_id = $_GET['upload_id'] ?? null;

if (!$upload_id) {
    die("Invalid upload ID.");
}

$successMessage = $errorMessage = "";

// Handle bulk image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../asset/gallery/uploads/' . $upload_id . '/';

    // Ensure the directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['images']['name'][$key]);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $filePath)) {
            $query = "INSERT INTO images (upload_id, file_name, file_path) VALUES ('$upload_id', '$fileName', '$filePath')";
            if (!$conn->query($query)) {
                $errorMessage = "Failed to save image metadata: " . $conn->error;
            }
        } else {
            $errorMessage = "Failed to upload image: $fileName";
        }
    }

    if (!$errorMessage) {
        $successMessage = "Images uploaded successfully.";
    }
}

// Fetch existing images for the upload
$query = "SELECT * FROM images WHERE upload_id = '$upload_id'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Upload</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'comp/sidebar.php'; ?>
        <main class="admin-main">
            <section class="upload-section">
                <h2>Manage Upload #<?php echo $upload_id; ?></h2>
                <div class="upload-card">
                    <?php if ($successMessage): ?>
                        <p class="success-message"><?php echo $successMessage; ?></p>
                    <?php elseif ($errorMessage): ?>
                        <p class="error-message"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-row">
                            <label for="images">Upload Images</label>
                            <input type="file" id="images" name="images[]" accept="image/*" multiple required>
                        </div>
                        <div class="form-row">
                            <button type="submit" class="upload-button">Upload Images</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="gallery-section">
                <h2>Uploaded Images</h2>
                <div class="modern-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="list-item">
                            <div class="list-image">
                                <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['file_name']); ?>">
                            </div>
                            <div class="list-actions">
                                <a href="delete_image.php?id=<?php echo $row['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
