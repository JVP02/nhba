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

// Fetch all users
$query = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($query);

$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch all user requests
$requestQuery = "SELECT * FROM user_requests ORDER BY id ASC";
$requestResult = $conn->query($requestQuery);

$userRequests = [];
if ($requestResult) {
    while ($row = $requestResult->fetch_assoc()) {
        $userRequests[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'comp/sidebar.php'; ?>
        <header>
            <!-- <h1>User Management</h1> -->
            <div class="profile-menu">
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
        <main>
            <section class="user-management">
                <h2>Manage Users</h2>
                <div class="user-table-container">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td>
                                        <button class="edit-button" onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                                        <button class="delete-button" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- <button class="add-user-button" onclick="addUser()">Add New User</button> -->
            </section>

            <section class="user-requests">
                <h2>User Requests</h2>
                <div class="user-requests-container">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userRequests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                                    <td><?php echo htmlspecialchars($request['name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                                    <td>
                                        <select id="role-<?php echo $request['id']; ?>">
                                            <option value="Admin">Admin</option>
                                            <option value="Editor">Editor</option>
                                            <option value="Viewer">Viewer</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="add-button" onclick="addUserRequest(<?php echo $request['id']; ?>)">Add</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script>
        function editUser(id) {
            window.location.href = `edit_user.php?id=${id}`;
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = `delete_user.php?id=${id}`;
            }
        }

        function addUser() {
            window.location.href = 'create_admin.php';
        }

        function addUserRequest(id) {
            const role = document.getElementById(`role-${id}`).value;
            if (confirm(`Are you sure you want to add this user with the role "${role}"?`)) {
                window.location.href = `add_user_request.php?id=${id}&role=${role}`;
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
