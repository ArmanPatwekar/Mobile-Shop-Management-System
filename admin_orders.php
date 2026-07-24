<?php session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: Adminlogin.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$orders_result = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.order_date DESC");
$orders_arr = [];
$status_counts = ['pending'=>0,'shipped'=>0,'completed'=>0,'cancelled'=>0];
while($o = $orders_result->fetch_assoc()) {
    $orders_arr[] = $o;
    if(isset($status_counts[$o['status']])) $status_counts[$o['status']]++;
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>📦 Orders | Admin Panel</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700}nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
.container{padding:25px;max-width:1200px;margin:0 auto}
h1{color:#1a1a2e;margin-bottom:20px;font-size:1.5em}.filter-bar{margin-bottom:15px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
select{padding:8px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em}
.stats-badges{display:flex;gap:8px;flex-wrap:wrap}.st-badge{padding:4px 12px;border-radius:20px;font-size:.78em;font-weight:600;cursor:pointer;transition:.3s;border:2px solid transparent}.st-badge:hover{opacity:.8}.st-badge.active{border-color:#1a1a2e}.st-badge.all{background:#1a1a2e;color:#fff}.st-badge.pending{background:#fff3e0;color:#e65100}.st-badge.shipped{background:#e3f2fd;color:#1565c0}.st-badge.completed{background:#e8f5e9;color:#2e7d32}.st-badge.cancelled{background:#ffebee;color:#c62828}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.08)}
th{background:#1a1a2e;color:#fff;padding:12px 10px;font-size:.85em;text-transform:uppercase}
td{padding:10px;border-bottom:1px solid #eee;font-size:.9em;text-align:center;vertical-align:middle}
tr:hover{background:#f8f9ff}select.status-select{padding:5px 10px;border-radius:6px;font-size:.85em;font-weight:600;cursor:pointer}
.status-completed{background:#e8f5e9;color:#2e7d32}.status-pending{background:#fff3e0;color:#e65100}
.status-shipped{background:#e3f2fd;color:#1565c0}.status-cancelled{background:#ffebee;color:#c62828}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:20px}
@media(max-width:768px){table{font-size:.8em}th,td{padding:8px 5px}}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY • ADMIN</div>
<nav><a href="index.php">📊 Dashboard</a><a href="sales_report.php">📈 Reports</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container"><h1>📦 Order Management <span id="orderCount" style="color:#888;font-size:.7em">(<?= count($orders_arr) ?>)</span></h1>
<div class="filter-bar">
<div class="stats-badges">
<span class="st-badge all active" data-filter="" onclick="filterByBadge(this)">📋 All (<?= count($orders_arr) ?>)</span>
<span class="st-badge pending" data-filter="pending" onclick="filterByBadge(this)">⏳ Pending (<?= $status_counts['pending'] ?>)</span>
<span class="st-badge shipped" data-filter="shipped" onclick="filterByBadge(this)">🚚 Shipped (<?= $status_counts['shipped'] ?>)</span>
<span class="st-badge completed" data-filter="completed" onclick="filterByBadge(this)">✅ Completed (<?= $status_counts['completed'] ?>)</span>
<span class="st-badge cancelled" data-filter="cancelled" onclick="filterByBadge(this)">❌ Cancelled (<?= $status_counts['cancelled'] ?>)</span>
</div>
<input type="text" id="searchOrder" placeholder="🔍 Search by customer or order #..." onkeyup="filterOrders()" style="padding:8px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em;flex:1;min-width:200px">
</div>
<table><thead><tr><th>#</th><th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Date</th><th>Status</th></tr></thead>
<tbody><?php $sn=1; foreach($orders_arr as $order): ?>
<tr class="order-row" data-status="<?= $order['status'] ?>">
<td><?= $sn++ ?></td><td>#<?= $order['id'] ?></td><td><?= $order['user_name'] ?></td>
<td>₹<?= number_format($order['total_amount'],2) ?></td>
<td><?= $order['payment_method'] ?></td>
<td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
<td><select class="status-select status-<?= $order['status'] ?>" data-id="<?= $order['id'] ?>" onchange="updateStatus(this)">
<option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>Pending</option>
<option value="shipped" <?= $order['status']=='shipped'?'selected':'' ?>>Shipped</option>
<option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>Completed</option>
<option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
</select></td></tr>
<?php endforeach; ?></tbody></table></div>
<script>
function updateStatus(el){const id=el.dataset.id,status=el.value;
fetch('update_order_status.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&status='+status}).then(r=>r.json()).then(d=>{if(d.success)location.reload()})}
function filterByBadge(el){
document.querySelectorAll('.st-badge').forEach(b=>b.classList.remove('active'));
el.classList.add('active');
const f=el.dataset.filter;
document.getElementById('status-filter').value=f;
filterOrders();
}
function filterOrders(){const f=document.querySelector('.st-badge.active')?.dataset?.filter||'',s=document.getElementById('searchOrder').value.toLowerCase();
let vis=0;
document.querySelectorAll('.order-row').forEach(r=>{const m=(!f||r.dataset.status===f)&&(!s||r.textContent.toLowerCase().includes(s));r.style.display=m?'':'none';if(m)vis++});
document.getElementById('orderCount').textContent='('+vis+' visible)'}
</script>
<footer>&copy; 2025 Phone Phactory Admin Panel</footer></body></html>

