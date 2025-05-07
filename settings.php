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
    if (isset($_POST['site_title'])) {
        $siteTitle = $conn->real_escape_string($_POST['site_title']);
        $adminEmail = $conn->real_escape_string($_POST['admin_email']);

        $updateQuery = "UPDATE settings SET site_title = '$siteTitle', admin_email = '$adminEmail' WHERE id = 1";
        if ($conn->query($updateQuery)) {
            $successMessage = "Settings updated successfully.";
        } else {
            $errorMessage = "Failed to update settings: " . $conn->error;
        }
    }

    if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        $adminId = $_SESSION['admin_id'];
        $query = "SELECT password FROM admin_accounts WHERE id = $adminId";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if ($currentPassword === $admin['password']) { // Plain-text password comparison
                if ($newPassword === $confirmPassword) {
                    $updatePasswordQuery = "UPDATE admin_accounts SET password = '$newPassword' WHERE id = $adminId";
                    if ($conn->query($updatePasswordQuery)) {
                        $successMessage = "Password updated successfully.";
                    } else {
                        $errorMessage = "Failed to update password: " . $conn->error;
                    }
                } else {
                    $errorMessage = "New password and confirm password do not match.";
                }
            } else {
                $errorMessage = "Current password is incorrect.";
            }
        } else {
            $errorMessage = "Failed to fetch admin details.";
        }
    }
}

// Fetch current settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = $conn->query($query);
$settings = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'comp/sidebar.php'; ?>
        <main>
            <section class="settings-section">
                <h2>Settings</h2>
                <?php if ($successMessage): ?>
                    <p class="success-message"><?php echo $successMessage; ?></p>
                <?php elseif ($errorMessage): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <h3>Site Settings</h3>
                    <div class="form-group">
                        <label for="site_title">Site Title</label>
                        <input type="text" id="site_title" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Admin Email</label>
                        <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                    </div>
                    <button type="submit" class="save-button">Save Settings</button>
                </form>

                <form method="POST" action="">
                    <h3>Change Password</h3>
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="save-button">Change Password</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
