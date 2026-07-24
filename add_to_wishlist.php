<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}
$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$check = $conn->query("SELECT id FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
if ($check->num_rows > 0) {
    $conn->query("DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
    echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
} else {
    $conn->query("INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
    echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist!']);
}
$count = $conn->query("SELECT COUNT(*) as total FROM wishlist WHERE user_id=$user_id")->fetch_assoc()['total'];
echo json_encode(['success' => true, 'action' => $check->num_rows > 0 ? 'removed' : 'added', 'message' => $check->num_rows > 0 ? 'Removed from wishlist' : 'Added to wishlist!', 'wish_count' => $count]);
$conn->close();
?>
