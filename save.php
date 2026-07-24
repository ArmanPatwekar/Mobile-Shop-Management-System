<?php
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) { die("Connection failed"); }
$name = $conn->real_escape_string($_POST['name']);
$mobile = $conn->real_escape_string($_POST['mobile']);
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];
$hash = password_hash($password, PASSWORD_DEFAULT);
$check = $conn->query("SELECT id FROM users WHERE email='$email'");
if ($check->num_rows > 0) {
    echo "<script>alert('Email already registered!');window.location='signup.html';</script>";
    exit;
}
$sql = "INSERT INTO users (name, mobile, email, password) VALUES ('$name','$mobile','$email','$hash')";
if ($conn->query($sql)) {
    header("Location: login.html");
    exit;
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>

