<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    die("Connection failed");
}
$user_id = $_SESSION['user_id'];
$name = $conn->real_escape_string($_POST['name']);
$mobile = $conn->real_escape_string($_POST['mobile']);
$email = $conn->real_escape_string($_POST['email']);
$address = $conn->real_escape_string($_POST['address']);
$sql = "UPDATE users SET name='$name', mobile='$mobile', email='$email', address='$address' WHERE id=$user_id";
if ($conn->query($sql) === TRUE) {
    header("Location: profile.php?updated=1");
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
