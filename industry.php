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

$successMessage = $errorMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $headline = $conn->real_escape_string($_POST['headline']);
    $summary = $conn->real_escape_string($_POST['summary']);
    $url = $conn->real_escape_string($_POST['url']);
    $thumbnailDir = '../asset/gallery/industry/thumbnails/';

    // Ensure the thumbnails directory exists
    if (!is_dir($thumbnailDir)) {
        mkdir($thumbnailDir, 0777, true);
    }

    $thumbnailFile = $thumbnailDir . basename($_FILES['thumbnail']['name']);

    // Move uploaded thumbnail to the server
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailFile)) {
        // Store file paths in the database
        $query = "INSERT INTO industry_update (headline, summary, thumbnail_url, url, date) 
                  VALUES ('$headline', '$summary', '$thumbnailFile', '$url', NOW())";
        if ($conn->query($query)) {
            $successMessage = "Industry update uploaded successfully.";
        } else {
            $errorMessage = "Failed to save update to the database: " . $conn->error;
        }
    } else {
        $errorMessage = "Failed to upload thumbnail. Please check the directory permissions.";
    }
}

// Fetch updates from the database
$query = "SELECT * FROM industry_update ORDER BY date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Updates</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'comp/sidebar.php'; ?>
        <main class="admin-main">
            <section class="upload-section">
                <h2>Upload Industry Update</h2>
                <div class="upload-card modern-card">
                    <?php if ($successMessage): ?>
                        <p class="success-message"><?php echo $successMessage; ?></p>
                    <?php elseif ($errorMessage): ?>
                        <p class="error-message"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
                    <form id="upload-form" method="POST" action="" enctype="multipart/form-data">
                        <div class="form-row">
                            <label for="headline"><i class="fas fa-heading"></i> Headline</label>
                            <input type="text" id="headline" name="headline" placeholder="Enter headline" required>
                        </div>
                        <div class="form-row">
                            <label for="summary"><i class="fas fa-align-left"></i> Summary</label>
                            <textarea id="summary" name="summary" placeholder="Enter a brief summary" required></textarea>
                        </div>
                        <div class="form-row">
                            <label for="url"><i class="fas fa-link"></i> URL</label>
                            <input type="url" id="url" name="url" placeholder="Enter URL (e.g., https://example.com)" required>
                        </div>
                        <div class="form-row">
                            <label for="thumbnail"><i class="fas fa-image"></i> Upload Thumbnail</label>
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" required>
                        </div>
                        <div class="form-row">
                            <button type="submit" class="upload-button"><i class="fas fa-upload"></i> Upload</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="gallery-section">
                <h2>Industry Updates</h2>
                <div class="modern-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="list-item">
                            <div class="list-image">
                                <img src="<?php echo htmlspecialchars($row['thumbnail_url']); ?>" alt="<?php echo htmlspecialchars($row['headline']); ?>">
                            </div>
                            <div class="list-content">
                                <h3><?php echo htmlspecialchars($row['headline']); ?></h3>
                                <p><?php echo htmlspecialchars($row['summary']); ?></p>
                                <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank" class="read-more-button">Read More</a>
                                <small><?php echo htmlspecialchars($row['date']); ?></small>
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
