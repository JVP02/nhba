<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nhb_admin';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = 'JVP02';
$email = 'johnvincentperalta09@gmail.com';
$password = password_hash('adminjvp', PASSWORD_BCRYPT);

$query = "INSERT INTO admin_accounts (name, email, password) VALUES ('$name', '$email', '$password')";
if ($conn->query($query)) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
