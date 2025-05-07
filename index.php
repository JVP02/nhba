<!-- filepath: c:\xampp\htdocs\nhb\admin\index.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for dashboard
$totalUploads = $conn->query("SELECT COUNT(*) AS total FROM community_update")->fetch_assoc()['total'];
$activeUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$pendingApprovals = 5; // Example static value

// Fetch recent uploads
$query = "SELECT * FROM community_update ORDER BY date DESC LIMIT 5";
$result = $conn->query($query);

$recentUploads = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentUploads[] = $row;
    }
}

// Fetch the total number of community updates
$query = "SELECT COUNT(*) AS total_updates FROM community_update";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $totalUpdates = $row['total_updates'];
} else {
    $totalUpdates = 0;
}

// Fetch the latest community updates
$query = "SELECT * FROM community_update ORDER BY date DESC LIMIT 5";
$result = $conn->query($query);

$updates = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $updates[] = $row;
    }
}

// Fetch users
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'comp/sidebar.php'; ?>
        <main>
            <header>
                <div class="profile-menu" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <div class="dropdown">
                        <button class="dropdown-button">Profile</button>
                        <div class="dropdown-content">
                            <a href="profile.php">View Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <section id="dashboard" class="dashboard-section">
                <h2>Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Uploads</h3>
                        <p id="total-uploads"><?php echo $totalUploads; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Active Users</h3>
                        <p id="active-users"><?php echo $activeUsers; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Pending Approvals</h3>
                        <p id="pending-approvals"><?php echo $pendingApprovals; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Community Updates</h3>
                        <p><?php echo $totalUpdates; ?></p>
                    </div>
                </div>
            </section>

            <section id="recent-updates" class="recent-updates-section">
                <h2>Recent Community Updates</h2>
                <div class="recent-updates-list">
                    <?php foreach ($updates as $update): ?>
                        <div class="update-item">
                            <div class="update-image">
                                <img src="<?php echo isset($update['image_url']) ? htmlspecialchars($update['image_url']) : ''; ?>" alt="<?php echo isset($update['title']) ? htmlspecialchars($update['title']) : 'Untitled'; ?>">
                            </div>
                            <div class="update-content">
                                <h3><?php echo isset($update['title']) ? htmlspecialchars($update['title']) : 'Untitled'; ?></h3>
                                <p><?php echo isset($update['description']) ? htmlspecialchars($update['description']) : 'No description available.'; ?></p>
                                <small><?php echo isset($update['date']) ? htmlspecialchars($update['date']) : 'Unknown date'; ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="users" class="users-section">
                <h2>User Management</h2>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td>
                                    <button class="action-btn edit-btn" data-id="<?php echo $user['id']; ?>">Edit</button>
                                    <button class="action-btn delete-btn" data-id="<?php echo $user['id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <section id="settings" class="settings-section">
                <h2>Settings</h2>
                <p>Manage your account and application settings here.</p>
            </section>
        </main>
    </div>
    <script src="js/admin.js"></script>
</body>
</html>
<?php $conn->close(); ?>