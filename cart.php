<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$user_id = $_SESSION['user_id'];
$cart_items = $conn->query("SELECT c.*, p.product_name, p.product_price, p.product_image, p.stock FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
$total = 0;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>🛒 Shopping Cart | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700;display:flex;align-items:center;gap:6px}
nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
nav a:hover{background:#e94560}
.container{flex:1;max-width:900px;margin:30px auto;padding:0 20px;width:100%}
h1{color:#1a1a2e;margin-bottom:20px;font-size:1.6em}
.cart-item{background:#fff;border-radius:12px;padding:15px;margin-bottom:12px;display:flex;align-items:center;gap:15px;box-shadow:0 2px 10px rgba(0,0,0,.06);animation:fadeIn .3s ease;transition:.3s}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.cart-item:hover{box-shadow:0 4px 15px rgba(0,0,0,.1)}
.cart-item img{width:70px;height:70px;object-fit:contain;border-radius:8px;background:#f0f2f5;padding:4px}
.item-info{flex:1}
.item-info h3{font-size:1em;color:#1a1a2e}
.item-info .price{color:#e94560;font-weight:700;font-size:1.1em}
.qty-control{display:flex;align-items:center;gap:8px;margin-top:5px}
.qty-control button{background:#0f3460;color:#fff;border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:1em;transition:.3s}
.qty-control button:hover{background:#e94560}
.qty-control span{font-weight:600;width:30px;text-align:center}
.remove-btn{background:#e94560;color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;font-weight:600;transition:.3s;font-size:.85em}
.remove-btn:hover{background:#d63851}
.cart-summary{background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-top:20px;text-align:right;animation:fadeIn .5s ease}
.cart-summary h2{color:#1a1a2e;font-size:1.3em}
.cart-summary .grand{color:#e94560;font-size:1.5em;font-weight:700}
.checkout-btn{display:inline-block;padding:12px 30px;background:linear-gradient(135deg,#e94560,#0f3460);color:#fff;text-decoration:none;border-radius:25px;font-weight:600;margin-top:10px;transition:.3s}
.checkout-btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(233,69,96,.4)}
.empty{text-align:center;padding:60px 20px;color:#888}
.empty span{font-size:4em;display:block;margin-bottom:15px}
.continue{display:inline-block;padding:10px 25px;background:#0f3460;color:#fff;text-decoration:none;border-radius:25px;margin-top:10px}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
@media(max-width:600px){.cart-item{flex-wrap:wrap}}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="user.php">🛍️ Shop</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container">
<h1>🛒 Your Shopping Cart <span id="itemCount" style="color:#888;font-size:.7em"></span></h1>
<div id="cartContainer">
<?php if ($cart_items->num_rows > 0): while($item = $cart_items->fetch_assoc()):
$subtotal = $item['product_price'] * $item['quantity'];
$total += $subtotal; ?>
<div class="cart-item" data-id="<?= $item['id'] ?>">
<img src="uploads/<?= $item['product_image'] ?>" alt="<?= $item['product_name'] ?>">
<div class="item-info">
<h3><?= $item['product_name'] ?></h3>
<div class="price">₹<?= number_format($item['product_price'],2) ?></div>
<div class="qty-control">
<button class="qty-minus" data-id="<?= $item['id'] ?>">−</button>
<span class="qty"><?= $item['quantity'] ?></span>
<button class="qty-plus" data-id="<?= $item['id'] ?>">+</button>
</div>
</div>
<button class="remove-btn" data-id="<?= $item['id'] ?>">🗑️ Remove</button>
</div>
<?php endwhile; ?>
<div class="cart-summary">
<h2>Total: <span class="grand">₹<?= number_format($total,2) ?></span></h2>
<a href="checkout.php" class="checkout-btn">💳 Proceed to Checkout</a>
</div>
<?php else: ?>
<div class="empty"><span>🛒</span><h2>Your cart is empty</h2><p>Browse our products and add items you love!</p>
<a href="user.php" class="continue">🛍️ Continue Shopping</a></div>
<?php endif; ?>
</div></div>
<script>
document.querySelectorAll('.qty-plus').forEach(b=>b.onclick=function(){
let id=this.dataset.id,span=this.parentElement.querySelector('.qty'),v=parseInt(span.textContent)+1;
this.style.transform='scale(0.9)';setTimeout(()=>this.style.transform='',200);
fetch('update_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&qty='+v}).then(r=>r.json()).then(d=>{if(d.success)location.reload()})});
document.querySelectorAll('.qty-minus').forEach(b=>b.onclick=function(){
let id=this.dataset.id,span=this.parentElement.querySelector('.qty'),v=Math.max(1,parseInt(span.textContent)-1);
this.style.transform='scale(0.9)';setTimeout(()=>this.style.transform='',200);
fetch('update_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&qty='+v}).then(r=>r.json()).then(d=>{if(d.success)location.reload()})});
document.querySelectorAll('.remove-btn').forEach(b=>b.onclick=function(){
if(confirm('Remove this item?')){this.textContent='⏳';fetch('remove_from_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+this.dataset.id}).then(r=>r.json()).then(d=>{if(d.success)location.reload()})}});
document.getElementById('itemCount').textContent='('+document.querySelectorAll('.cart-item').length+' items)';
</script>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

