<?php
header('Content-Type: application/json');
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) { echo json_encode([]); exit; }
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$category = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : '';
$sql = "SELECT * FROM products WHERE 1=1";
if ($search) $sql .= " AND (product_name LIKE '%$search%' OR product_description LIKE '%$search%')";
if ($category) $sql .= " AND product_category = '$category'";
$sql .= " ORDER BY id DESC LIMIT 50";
$result = $conn->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['id'],
        'name' => $row['product_name'],
        'category' => $row['product_category'],
        'price' => $row['product_price'],
        'image' => $row['product_image'],
        'description' => substr($row['product_description'], 0, 100),
        'rating' => $row['rating_avg']
    ];
}
echo json_encode($products);
$conn->close();
?>
