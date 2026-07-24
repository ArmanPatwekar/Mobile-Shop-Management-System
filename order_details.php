<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$order_id = intval($_GET['id']);
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id={$_SESSION['user_id']}")->fetch_assoc();
if (!$order) { die("Order not found"); }
$items = $conn->query("SELECT oi.*, p.product_name, p.product_image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$order_id");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Order #<?= $order_id ?> | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center}
.logo{font-size:1.2em;font-weight:700}nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;background:rgba(255,255,255,.1);transition:.3s}nav a:hover{background:#e94560}
.container{flex:1;max-width:800px;margin:30px auto;padding:0 20px;width:100%}
.card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:15px}
h1{color:#1a1a2e;margin-bottom:5px;font-size:1.4em}.sub{color:#888;font-size:.9em;margin-bottom:15px}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:15px}
@media(max-width:500px){.info-grid{grid-template-columns:1fr}}
.info-item{padding:8px;background:#f8f9ff;border-radius:8px}
.info-item label{font-size:.8em;color:#888;display:block}
.info-item span{font-weight:600;color:#1a1a2e}
.item-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f0f0f0}
.item-row img{width:50px;height:50px;object-fit:contain;border-radius:6px;background:#f0f2f5;padding:4px}
.item-row .name{flex:1;font-weight:600;font-size:.9em}
.item-row .qty{color:#888;font-size:.85em}
.item-row .price{font-weight:700;color:#e94560}
.total-bar{display:flex;justify-content:space-between;padding:12px 0;font-size:1.2em;font-weight:700;border-top:2px solid #eee;margin-top:10px}
.total-bar span:last-child{color:#e94560}
.status-badge{display:inline-block;padding:6px 20px;border-radius:20px;font-weight:600;font-size:.9em}
.shipping-addr{background:#f8f9ff;padding:12px;border-radius:8px;margin:10px 0;font-size:.9em;color:#555;line-height:1.5}
.back-btn{display:inline-block;padding:10px 25px;background:#0f3460;color:#fff;text-decoration:none;border-radius:25px;font-weight:600;margin-top:10px}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="my_orders.php">← My Orders</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container">
<div class="card"><h1>📦 Order #<?= $order_id ?></h1>
<p class="sub">Placed on <?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></p>
<div style="text-align:right;margin-bottom:10px">
<span class="status-badge" style="background:#e8f5e9;color:#2e7d32"><?= ucfirst($order['status']) ?></span></div>
<div class="info-grid">
<div class="info-item"><label>Payment Method</label><span>💳 <?= $order['payment_method'] ?></span></div>
<div class="info-item"><label>Payment ID</label><span>🆔 <?= $order['payment_id'] ?></span></div>
<div class="info-item"><label>Order Total</label><span>₹<?= number_format($order['total_amount'],2) ?></span></div>
<div class="info-item"><label>Status</label><span>📦 <?= ucfirst($order['status']) ?></span></div></div>
<h3 style="margin-bottom:5px">📍 Shipping Address</h3>
<div class="shipping-addr"><?= nl2br($order['shipping_address']) ?></div>
<h3 style="margin:15px 0 10px">🛒 Items Ordered</h3>
<?php while($item=$items->fetch_assoc()): ?>
<div class="item-row">
<img src="uploads/<?= $item['product_image'] ?>" alt="<?= $item['product_name'] ?>">
<span class="name"><?= $item['product_name'] ?></span>
<span class="qty">×<?= $item['quantity'] ?></span>
<span class="price">₹<?= number_format($item['price']*$item['quantity'],2) ?></span></div>
<?php endwhile; ?>
<div class="total-bar"><span>Total Paid</span><span>₹<?= number_format($order['total_amount'],2) ?></span></div>
<a href="my_orders.php" class="back-btn">← Back to Orders</a>
<a href="generate_invoice.php?order_id=<?= $order_id ?>" class="back-btn" style="background:#e94560;margin-left:8px">📄 Download Invoice</a>
</div></div>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

