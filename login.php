<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) { die("Connection failed"); }
$name = $_POST['username'];
$password = $_POST['password'];
$sql = "SELECT * FROM users WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: user.php");
        exit;
    } else {
        echo "<script>alert('Incorrect Password');window.location='login.html';</script>";
    }
} else {
    echo "<script>alert('User not found');window.location='login.html';</script>";
}
$stmt->close();
$conn->close();
?>
