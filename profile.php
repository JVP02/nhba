<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="profile-page">
        <h1>Profile</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
        <a href="index.php" class="button">Back to Dashboard</a>
    </div>
</body>
</html>
