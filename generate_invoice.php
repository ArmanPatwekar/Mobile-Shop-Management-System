<?php session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) { die("Access denied"); }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$order_id = intval($_GET['order_id']);
$cond = isset($_SESSION['admin_id']) ? "id=$order_id" : "id=$order_id AND user_id={$_SESSION['user_id']}";
$order = $conn->query("SELECT * FROM orders WHERE $cond")->fetch_assoc();
if (!$order) { die("Order not found"); }
$items = $conn->query("SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$order_id");
$user = $conn->query("SELECT * FROM users WHERE id={$order['user_id']}")->fetch_assoc();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Invoice #<?= $order_id ?> | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:#fff;color:#333;padding:40px;max-width:800px;margin:0 auto}
.header{display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #1a1a2e;padding-bottom:20px;margin-bottom:20px}
.header h1{color:#1a1a2e;font-size:1.8em}.header .brand{color:#e94560;font-weight:700}
.invoice-info{display:flex;justify-content:space-between;margin-bottom:25px;flex-wrap:wrap;gap:10px}
.info-box{background:#f8f9ff;padding:15px;border-radius:8px;flex:1;min-width:180px}
.info-box label{font-size:.8em;color:#888;display:block}
.info-box span{font-weight:600;color:#1a1a2e;font-size:.95em}
table{width:100%;border-collapse:collapse;margin:20px 0}
th{background:#1a1a2e;color:#fff;padding:12px;text-align:left;font-size:.85em;text-transform:uppercase}
td{padding:12px;border-bottom:1px solid #eee;font-size:.9em}
.total-row{font-weight:700;font-size:1.1em;background:#f8f9ff}
.total-row td:last-child{color:#e94560;font-size:1.2em}
.footer{text-align:center;border-top:2px solid #eee;padding-top:20px;margin-top:30px;color:#888;font-size:.85em}
.print-btn{padding:12px 30px;background:#1a1a2e;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;margin-bottom:20px}
.print-btn:hover{background:#0f3460}
@media print{.print-btn{display:none}body{padding:20px}}
</style></head><body>
<button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
<div class="header"><div><h1>📱 PHONE PHACTORY</h1><p class="brand">Premium Mobile Store</p></div>
<div style="text-align:right"><h2 style="color:#e94560">INVOICE</h2><p style="color:#888">#<?= str_pad($order_id,6,'0',STR_PAD_LEFT) ?></p></div></div>
<div class="invoice-info">
<div class="info-box"><label>Bill To</label><span><?= $user['name'] ?></span><span style="display:block;font-weight:400;font-size:.85em;color:#666"><?= $user['email'] ?><br><?= $user['mobile'] ?></span></div>
<div class="info-box"><label>Order Date</label><span><?= date('d M Y', strtotime($order['order_date'])) ?></span><label style="margin-top:5px">Payment</label><span><?= ucfirst($order['payment_method']) ?></span></div>
<div class="info-box"><label>Order Status</label><span style="color:#2e7d32"><?= ucfirst($order['status']) ?></span><label style="margin-top:5px">Payment ID</label><span style="font-size:.8em"><?= $order['payment_id'] ?></span></div></div>
<table><thead><tr><th>#</th><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
<tbody><?php $i=1; while($item=$items->fetch_assoc()): ?>
<tr><td><?= $i++ ?></td><td><?= $item['product_name'] ?></td><td><?= $item['quantity'] ?></td>
<td>₹<?= number_format($item['price'],2) ?></td><td>₹<?= number_format($item['price']*$item['quantity'],2) ?></td></tr>
<?php endwhile; ?>
<tr class="total-row"><td colspan="4" style="text-align:right">Total Amount</td><td>₹<?= number_format($order['total_amount'],2) ?></td></tr>
</tbody></table>
<p style="color:#888;font-size:.85em;margin-top:10px">All prices are in Indian Rupees (₹). This is a computer-generated invoice.</p>
<div class="footer"><p>📱 Phone Phactory | 📞 +91-9876543210 | 📧 support@phonephactory.com</p>
<p>📍 123, Tech Park, Mumbai - 400001, India</p><p>&copy; 2025 Phone Phactory. All Rights Reserved.</p></div></body></html>

