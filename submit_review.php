<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'message'=>'Login required']);
    exit;
}
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) {
    echo json_encode(['success'=>false, 'message'=>'DB connection failed']);
    exit;
}
$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$rating = intval($_POST['rating']);
$review_text = $conn->real_escape_string($_POST['review_text']);
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success'=>false, 'message'=>'Invalid rating']);
    exit;
}
$check = $conn->query("SELECT id FROM reviews WHERE user_id=$user_id AND product_id=$product_id");
if ($check->num_rows > 0) {
    $conn->query("UPDATE reviews SET rating=$rating, review_text='$review_text' WHERE user_id=$user_id AND product_id=$product_id");
} else {
    $conn->query("INSERT INTO reviews (user_id, product_id, rating, review_text) VALUES ($user_id, $product_id, $rating, '$review_text')");
}
$avg = $conn->query("SELECT AVG(rating) as avg FROM reviews WHERE product_id=$product_id")->fetch_assoc()['avg'];
$avg = round($avg, 1);
$conn->query("UPDATE products SET rating_avg=$avg WHERE id=$product_id");
$conn->close();
echo json_encode(['success'=>true, 'message'=>'Review submitted!']);
?>
