<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}
$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity'] ?? 1);
$check = $conn->query("SELECT id, quantity FROM cart WHERE user_id=$user_id AND product_id=$product_id");
if ($check->num_rows > 0) {
    $row = $check->fetch_assoc();
    $new_qty = $row['quantity'] + $quantity;
    $conn->query("UPDATE cart SET quantity=$new_qty WHERE id={$row['id']}");
} else {
    $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
}
$count = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id=$user_id")->fetch_assoc()['total'] ?? 0;
echo json_encode(['success' => true, 'message' => 'Added to cart!', 'cart_count' => $count]);
$conn->close();
?>
