<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$cart_items = $conn->query("SELECT c.*, p.product_name, p.product_price, p.product_image, p.stock FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
if ($cart_items->num_rows == 0) { header("Location: cart.php"); exit; }
$total = 0;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>💳 Checkout | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center}
.logo{font-size:1.2em;font-weight:700}nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;background:rgba(255,255,255,.1);transition:.3s}
nav a:hover{background:#e94560}.container{flex:1;max-width:900px;margin:30px auto;padding:0 20px;width:100%}
h1{color:#1a1a2e;margin-bottom:20px}.checkout-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:25px}
@media(max-width:768px){.checkout-grid{grid-template-columns:1fr}}
.card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:15px}
.card h2{font-size:1.1em;color:#1a1a2e;margin-bottom:12px}
.order-item{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:.9em}
.order-item:last-child{border-bottom:none}
.total-line{display:flex;justify-content:space-between;padding:10px 0;font-weight:600;font-size:1.1em}
.grand-total{color:#e94560;font-size:1.3em;font-weight:700;text-align:right;padding:10px 0;border-top:2px solid #eee;margin-top:10px}
input,textarea{width:100%;padding:10px;border:2px solid #e0e0e0;border-radius:8px;margin:5px 0 12px;font-family:inherit;font-size:.95em}
input:focus,textarea:focus{border-color:#0f3460;outline:none}
textarea{resize:vertical;min-height:70px}
.pay-btn{width:100%;padding:14px;background:linear-gradient(135deg,#e94560,#0f3460);color:#fff;border:none;border-radius:8px;font-size:1.1em;font-weight:600;cursor:pointer;transition:.3s}
.pay-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(233,69,96,.4)}
.secure-badge{text-align:center;color:#888;font-size:.85em;margin-top:10px}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="cart.php">← Back to Cart</a></nav></header>
<div class="container"><h1>💳 Checkout</h1>
<div class="checkout-grid">
<div class="card">
<h2>📋 Shipping Address</h2>
<form id="checkout-form" method="POST" action="payment.php">
<input type="text" name="full_name" placeholder="Full Name" value="<?= $user['name'] ?>" required>
<input type="text" name="mobile" placeholder="Mobile Number" value="<?= $user['mobile'] ?>" required>
<input type="email" name="email" placeholder="Email" value="<?= $user['email'] ?>" required>
<textarea name="address" placeholder="Full Address with pincode" required><?= $user['address'] ?? '' ?></textarea>
<input type="hidden" name="total_amount" id="total_amount" value="0">
</div>
<div class="card">
<h2>🛒 Order Summary</h2>
<?php while($item=$cart_items->fetch_assoc()):
$subtotal = $item['product_price'] * $item['quantity'];
$total += $subtotal; ?>
<div class="order-item">
<span><?= $item['product_name'] ?> × <?= $item['quantity'] ?></span>
<span>₹<?= number_format($subtotal,2) ?></span>
</div>
<?php endwhile; ?>
<div class="total-line"><span>Subtotal</span><span>₹<?= number_format($total,2) ?></span></div>
<div class="total-line"><span>Shipping</span><span class="grand-total">FREE</span></div>
<div class="grand-total">Total: ₹<?= number_format($total,2) ?></div>
<button type="submit" class="pay-btn" onclick="document.getElementById('total_amount').value=<?= $total ?>">💳 Pay ₹<?= number_format($total,2) ?> via Razorpay</button>
<p class="secure-badge">🔒 100% Secure Payment • SSL Encrypted</p>
</div></form></div>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

