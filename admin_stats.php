<?php
session_start();
if (!isset($_SESSION['admin_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }
header('Content-Type: application/json');
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$period = $_GET['period'] ?? 'monthly';
$sales = [];
if ($period == 'daily') {
    $result = $conn->query("SELECT DATE(order_date) as date, SUM(total_amount) as total FROM orders WHERE status!='cancelled' GROUP BY DATE(order_date) ORDER BY date ASC LIMIT 15");
    while($r=$result->fetch_assoc()) $sales[] = ['label'=>date('d M',strtotime($r['date'])), 'total'=>floatval($r['total'])];
} elseif ($period == 'yearly') {
    $result = $conn->query("SELECT YEAR(order_date) as year, SUM(total_amount) as total FROM orders WHERE status!='cancelled' GROUP BY YEAR(order_date) ORDER BY year ASC");
    while($r=$result->fetch_assoc()) $sales[] = ['label'=>$r['year'], 'total'=>floatval($r['total'])];
} else {
    $result = $conn->query("SELECT DATE_FORMAT(order_date,'%b %Y') as month, SUM(total_amount) as total FROM orders WHERE status!='cancelled' GROUP BY YEAR(order_date), MONTH(order_date) ORDER BY MIN(order_date) ASC LIMIT 12");
    while($r=$result->fetch_assoc()) $sales[] = ['label'=>$r['month'], 'total'=>floatval($r['total'])];
}
$total_products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as t FROM orders WHERE status!='cancelled'")->fetch_assoc()['t'] ?? 0;
$total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$cat_data = $conn->query("SELECT product_category, COUNT(*) as c FROM products GROUP BY product_category");
$categories = [];
while($r=$cat_data->fetch_assoc()) $categories[] = ['label'=>$r['product_category'], 'count'=>intval($r['c'])];
$brand_data = $conn->query("SELECT product_brand, COUNT(*) as c FROM products GROUP BY product_brand ORDER BY c DESC");
$brands = [];
while($r=$brand_data->fetch_assoc()) $brands[] = ['label'=>$r['product_brand'], 'count'=>intval($r['c'])];
echo json_encode(['sales'=>$sales, 'total_products'=>$total_products, 'total_orders'=>$total_orders, 'total_revenue'=>$total_revenue, 'total_users'=>$total_users, 'categories'=>$categories, 'brands'=>$brands]);
$conn->close();
?>
