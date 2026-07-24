<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$order_id = intval($_GET['order_id'] ?? 0);
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id={$_SESSION['user_id']}")->fetch_assoc();
if (!$order) { header("Location: user.php"); exit; }
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>✅ Order Success | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}
.card{background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.1);max-width:450px;width:90%}
.icon{font-size:5em;margin-bottom:15px}
h1{color:#1a1a2e;font-size:1.6em;margin-bottom:8px}
p{color:#666;line-height:1.6;margin-bottom:5px;font-size:.95em}
.order-id{background:#f0f2f5;padding:10px;border-radius:8px;font-weight:700;color:#0f3460;margin:15px 0;font-size:1.1em}
.actions{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;justify-content:center}
.actions a{padding:12px 25px;border-radius:25px;text-decoration:none;font-weight:600;transition:.3s}
.btn-primary{background:linear-gradient(135deg,#e94560,#0f3460);color:#fff}
.btn-secondary{background:#0f3460;color:#fff}
.btn-primary:hover,.btn-secondary:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,.2)}
.confetti{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;overflow:hidden}
.piece{position:absolute;width:10px;height:10px;opacity:.7;animation:fall linear forwards}
@keyframes fall{0%{transform:translateY(-10px) rotate(0);opacity:1}100%{transform:translateY(100vh) rotate(720deg);opacity:0}}
</style></head><body>
<div class="confetti" id="confetti"></div>
<div class="card">
<div class="icon">🎉</div>
<h1>✅ Order Placed Successfully!</h1>
<p>Thank you for your purchase! Your order has been confirmed and will be shipped within 3-5 business days.</p>
<div class="order-id">📦 Order #<?= $order_id ?></div>
<p>💳 Paid: ₹<?= number_format($order['total_amount'],2) ?></p>
<p>📧 A confirmation email will be sent shortly.</p>
<div class="actions">
<a href="my_orders.php" class="btn-primary">📋 View My Orders</a>
<a href="user.php" class="btn-secondary">🛍️ Continue Shopping</a>
</div></div>
<script>
(function(){const c=['#e94560','#0f3460','#ffc107','#4caf50','#2196f3'];for(let i=0;i<50;i++){const p=document.createElement('div');p.className='piece';p.style.left=Math.random()*100+'%';p.style.background=c[Math.floor(Math.random()*c.length)];p.style.width=(5+Math.random()*10)+'px';p.style.height=(5+Math.random()*10)+'px';p.style.borderRadius=Math.random()>.5?'50%':'2px';p.style.animationDuration=(2+Math.random()*3)+'s';p.style.animationDelay=Math.random()*2+'s';document.getElementById('confetti').appendChild(p)}})();
</script></body></html>

