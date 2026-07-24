<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    echo json_encode(['success' => false]);
    exit;
}
$id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];
$conn->query("DELETE FROM wishlist WHERE id=$id AND user_id=$user_id");
echo json_encode(['success' => $conn->affected_rows > 0]);
$conn->close();
?>
