<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    echo json_encode(['success' => false]);
    exit;
}
$id = intval($_POST['id']);
$status = $conn->real_escape_string($_POST['status']);
$valid = ['pending', 'shipped', 'completed', 'cancelled'];
if (!in_array($status, $valid)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}
$conn->query("UPDATE orders SET status='$status' WHERE id=$id");
echo json_encode(['success' => $conn->affected_rows > 0]);
$conn->close();
?>
