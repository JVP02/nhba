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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insert into user_requests table
    $query = "INSERT INTO user_requests (name, email, password) VALUES ('$name', '$email', '$password')";
    if ($conn->query($query)) {
        $successMessage = "Sign-in request submitted successfully!";
    } else {
        $errorMessage = "Failed to submit sign-in request: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="style/admin.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div class="signin-container">
        <h2>Sign In</h2>
        <?php if (isset($successMessage)): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php elseif (isset($errorMessage)): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="signin-button">Submit</button>
        </form>
        <div class="google-signin-container">
            <div id="g_id_onload"
                data-client_id="947174043300-2o3rsu4sak4q6v6s1kn6ue8g9alvifqm.apps.googleusercontent.com"
                data-context="signin"
                data-ux_mode="popup"
                data-callback="handleCredentialResponse"
                data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
                data-type="standard"
                data-shape="rectangular"
                data-theme="outline"
                data-text="continue_with"
                data-size="large"
                data-logo_alignment="left">
            </div>
        </div>
    </div>
    <script>
        function handleCredentialResponse(response) {
            const data = jwt_decode(response.credential);
            console.log(data);

            // Send Google user data to the server
            fetch('google_signin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: data.name,
                    email: data.email
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Google Sign-In successful!');
                    window.location.href = 'index.php';
                } else {
                    alert('Google Sign-In failed: ' + data.message);
                }
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode/build/jwt-decode.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
