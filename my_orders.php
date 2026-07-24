<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id=$user_id ORDER BY order_date DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>📋 My Orders | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700}nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
.container{flex:1;max-width:900px;margin:30px auto;padding:0 20px;width:100%}
h1{color:#1a1a2e;margin-bottom:20px}.filter-bar{margin-bottom:15px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.filter-bar select{padding:8px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em}.filter-bar input{padding:8px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em;flex:1;min-width:200px}
.order-card{background:#fff;border-radius:12px;padding:18px;margin-bottom:15px;box-shadow:0 2px 10px rgba(0,0,0,.06);transition:.3s;animation:fadeIn .4s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.order-card:hover{box-shadow:0 4px 15px rgba(0,0,0,.1)}
.order-header{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;margin-bottom:10px}
.order-id{font-weight:700;color:#0f3460;font-size:1.1em}
.order-date{color:#888;font-size:.85em}
.order-status{padding:4px 14px;border-radius:20px;font-size:.8em;font-weight:600}
.status-completed{background:#e8f5e9;color:#2e7d32}
.status-pending{background:#fff3e0;color:#e65100}
.status-shipped{background:#e3f2fd;color:#1565c0}
.status-cancelled{background:#ffebee;color:#c62828}
.order-total{font-size:1.2em;font-weight:700;color:#e94560}
.order-actions{margin-top:10px;display:flex;gap:8px}
.order-actions a{padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:600;font-size:.85em;transition:.3s}
.view-btn{background:#0f3460;color:#fff}
.invoice-btn{background:#e94560;color:#fff}
.view-btn:hover,.invoice-btn:hover{transform:translateY(-2px)}
.empty{text-align:center;padding:60px 20px;color:#888}
.empty span{font-size:4em;display:block;margin-bottom:15px}
.continue{display:inline-block;padding:10px 25px;background:#0f3460;color:#fff;text-decoration:none;border-radius:25px;margin-top:10px}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="user.php">🛍️ Shop</a><a href="profile.php">👤 Profile</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container"><h1>📋 My Orders <span id="orderCount" style="color:#888;font-size:.7em"></span></h1>
<?php if ($orders->num_rows > 0): ?>
<div class="filter-bar">
<select id="statusFilter" onchange="filterOrders()">
<option value="">📋 All Statuses</option>
<option value="pending">⏳ Pending</option>
<option value="shipped">🚚 Shipped</option>
<option value="completed">✅ Completed</option>
<option value="cancelled">❌ Cancelled</option>
</select>
<input type="text" id="searchOrders" placeholder="🔍 Search by order # or payment..." onkeyup="filterOrders()">
</div>
<div id="ordersList">
<?php while($order=$orders->fetch_assoc()): ?>
<div class="order-card order-row" data-status="<?= $order['status'] ?>">
<div class="order-header">
<span class="order-id">📦 Order #<?= $order['id'] ?></span>
<span class="order-date"><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></span>
<span class="order-status status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
</div>
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap">
<span>💳 <?= $order['payment_method'] ?> | 🆔 <?= substr($order['payment_id'],0,12) ?>...</span>
<span class="order-total">₹<?= number_format($order['total_amount'],2) ?></span>
</div>
<div class="order-actions">
<a href="order_details.php?id=<?= $order['id'] ?>" class="view-btn">📄 View Details</a>
<a href="generate_invoice.php?order_id=<?= $order['id'] ?>" class="invoice-btn">📄 Download Invoice</a>
</div></div>
<?php endwhile; ?>
</div>
<?php else: ?>
<div class="empty"><span>📦</span><h2>No orders yet</h2><p>Start shopping and place your first order!</p>
<a href="user.php" class="continue">🛍️ Browse Products</a></div>
<?php endif; ?></div>
<script>
function filterOrders(){
const f=document.getElementById('statusFilter').value;
const s=document.getElementById('searchOrders').value.toLowerCase();
let vis=0;
document.querySelectorAll('.order-row').forEach(r=>{
const m=(!f||r.dataset.status===f)&&(!s||r.textContent.toLowerCase().includes(s));
r.style.display=m?'':'none';if(m)vis++});
document.getElementById('orderCount').textContent='('+vis+' visible)';
}
filterOrders();
</script>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

