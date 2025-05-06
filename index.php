<!-- filepath: c:\xampp\htdocs\nhb\admin\index.php -->
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

// Fetch data for dashboard
$totalUploads = $conn->query("SELECT COUNT(*) AS total FROM uploads")->fetch_assoc()['total'];
$activeUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$pendingApprovals = 5; // Example static value

// Fetch recent uploads
$recentUploads = $conn->query("SELECT * FROM uploads ORDER BY uploaded_at DESC LIMIT 5");

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
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="#dashboard">Dashboard</a></li>
                    <li><a href="#uploads">Uploads</a></li>
                    <li><a href="#users">User Management</a></li>
                    <li><a href="#settings">Settings</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
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
                </div>
            </section>

            <section id="uploads" class="uploads-section">
                <h2>Recent Uploads</h2>
                <div class="uploads-grid">
                    <?php while ($upload = $recentUploads->fetch_assoc()): ?>
                        <div class="upload-card">
                            <img src="<?php echo $upload['url']; ?>" alt="<?php echo $upload['name']; ?>">
                            <p><?php echo $upload['name']; ?></p>
                        </div>
                    <?php endwhile; ?>
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