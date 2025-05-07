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

// Handle career creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_career'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $qualifications = $conn->real_escape_string(implode("\n", $_POST['qualifications'])); // Combine qualifications into plain text
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);
    $category = $conn->real_escape_string($_POST['category']);

    // Handle image upload
    $uploadDir = '../asset/careers/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $imageName = basename($_FILES['image']['name']);
    $imagePath = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        $imageUrl = 'asset/careers/' . $imageName; // Save relative path for database

        $query = "INSERT INTO career (title, qualifications, description, location, category, image_url) 
                  VALUES ('$title', '$qualifications', '$description', '$location', '$category', '$imageUrl')";
        if ($conn->query($query)) {
            $successMessage = "Career created successfully.";
        } else {
            $errorMessage = "Failed to create career: " . $conn->error;
        }
    } else {
        $errorMessage = "Failed to upload image. Please check directory permissions.";
    }
}

// Handle career deletion
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $query = "DELETE FROM career WHERE id = $deleteId";
    if ($conn->query($query)) {
        $successMessage = "Career deleted successfully.";
    } else {
        $errorMessage = "Failed to delete career: " . $conn->error;
    }
}

// Fetch all careers
$query = "SELECT * FROM career ORDER BY id ASC";
$result = $conn->query($query);

$careers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $careers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers Admin</title>
    <link rel="stylesheet" href="style/admin.css">
    <script>
        // Add new qualification row
        function addQualificationRow() {
            const container = document.getElementById('qualifications-container');
            const newRow = document.createElement('div');
            newRow.className = 'qualification-row';
            newRow.innerHTML = `
                <input type="text" name="qualifications[]" placeholder="Enter qualification" required>
                <button type="button" onclick="removeQualificationRow(this)">Remove</button>
            `;
            container.appendChild(newRow);
        }

        // Remove a qualification row
        function removeQualificationRow(button) {
            const row = button.parentElement;
            row.remove();
        }
    </script>
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'comp/sidebar.php'; ?>
        <main>
            <section class="careers-admin">
                <h2>Careers Management</h2>
                <?php if ($successMessage): ?>
                    <p class="success-message"><?php echo $successMessage; ?></p>
                <?php elseif ($errorMessage): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>

                <!-- Career Creation Form -->
                <form method="POST" action="" enctype="multipart/form-data" class="career-form">
                    <h3>Create New Career</h3>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="qualifications">Qualifications</label>
                        <div id="qualifications-container">
                            <div class="qualification-row">
                                <input type="text" name="qualifications[]" placeholder="Enter qualification" required>
                                <button type="button" onclick="removeQualificationRow(this)">Remove</button>
                            </div>
                        </div>
                        <button type="button" onclick="addQualificationRow()">Add Qualification</button>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="Bulacan, Philippines" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="Production">Production</option>
                            <option value="Management">Management</option>
                            <option value="Professional">Professional</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Upload Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="create_career" class="save-button">Create Career</button>
                </form>

                <!-- Careers Table -->
                <h3>Existing Careers</h3>
                <div class="careers-table-container">
                    <table class="careers-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($careers as $career): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($career['id']); ?></td>
                                    <td><?php echo htmlspecialchars($career['title']); ?></td>
                                    <td><?php echo htmlspecialchars($career['category']); ?></td>
                                    <td><?php echo htmlspecialchars($career['location']); ?></td>
                                    <td>
                                        <a href="edit_career.php?id=<?php echo $career['id']; ?>" class="edit-button">Edit</a>
                                        <a href="?delete_id=<?php echo $career['id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this career?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
