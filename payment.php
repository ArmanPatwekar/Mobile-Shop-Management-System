<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$user_id = $_SESSION['user_id'];
$total_amount = floatval($_POST['total_amount']);
$address = $conn->real_escape_string($_POST['address']);
$full_name = $conn->real_escape_string($_POST['full_name']);
$mobile = $conn->real_escape_string($_POST['mobile']);
$email = $conn->real_escape_string($_POST['email']);
$shipping = "Name: $full_name, Mobile: $mobile, Email: $email, Address: $address";
$cart_items = $conn->query("SELECT c.*, p.product_price, p.product_name FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
if ($cart_items->num_rows == 0) { header("Location: cart.php"); exit; }
// Deduct stock
while ($item = $cart_items->fetch_assoc()) {
    $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']} AND stock >= {$item['quantity']}");
}
$conn->query("INSERT INTO orders (user_id, total_amount, status, payment_method, payment_id, shipping_address) VALUES ($user_id, $total_amount, 'completed', 'razorpay', 'RZP_" . time() . "', '$shipping')");
$order_id = $conn->insert_id;
$cart_items2 = $conn->query("SELECT c.*, p.product_price FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
while ($item = $cart_items2->fetch_assoc()) {
    $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['product_price']})");
}
$conn->query("DELETE FROM cart WHERE user_id=$user_id");
$conn->close();
header("Location: payment_success.php?order_id=$order_id");
exit;
?>
