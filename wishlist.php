<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$user_id = $_SESSION['user_id'];
$items = $conn->query("SELECT w.id as wid, p.* FROM wishlist w JOIN products p ON w.product_id=p.id WHERE w.user_id=$user_id");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>❤️ Wishlist | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700;display:flex;align-items:center;gap:6px}
nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
nav a:hover{background:#e94560}
.container{flex:1;max-width:1000px;margin:30px auto;padding:0 20px;width:100%}
h1{color:#1a1a2e;margin-bottom:20px;font-size:1.6em}
.filter-bar{margin-bottom:15px}
.filter-bar input{padding:8px 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em;width:100%;max-width:350px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;animation:fadeIn .4s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.card{background:#fff;border-radius:12px;padding:15px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.06);transition:.4s;position:relative}
.card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,.12)}
.card img{width:100%;height:140px;object-fit:contain;border-radius:8px;background:#f8f8f8;padding:8px;margin-bottom:10px}
.card h3{font-size:1em;color:#1a1a2e;margin:5px 0 3px}
.card .price{color:#e94560;font-size:1.2em;font-weight:700;margin:8px 0}
.card .actions{display:flex;gap:8px}
.card .actions button{flex:1;padding:8px;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:.85em;transition:.3s}
.btn-cart{background:#0f3460;color:#fff}
.btn-cart:hover{background:#1a1a2e}
.remove-wish{background:#e94560;color:#fff}
.remove-wish:hover{background:#d63851}
.empty{text-align:center;padding:60px 20px;color:#888}
.empty span{font-size:4em;display:block;margin-bottom:15px}
.continue{display:inline-block;padding:10px 25px;background:#0f3460;color:#fff;text-decoration:none;border-radius:25px;margin-top:10px}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="user.php">🛍️ Shop</a><a href="cart.php">🛒 Cart</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container">
<h1>❤️ My Wishlist <span id="countBadge" style="color:#888;font-size:.7em"></span></h1>
<?php if ($items->num_rows > 0): ?>
<div class="filter-bar">
<input type="text" id="searchWish" placeholder="🔍 Filter by product name..." onkeyup="filterWishlist()">
</div>
<div class="grid" id="wishGrid"><?php while($row=$items->fetch_assoc()): ?>
<div class="card wish-item" data-name="<?= strtolower($row['product_name']) ?>">
<img src="uploads/<?= $row['product_image'] ?>" alt="<?= $row['product_name'] ?>">
<h3><?= $row['product_name'] ?></h3>
<h4 style="color:#888;font-size:.85em;font-weight:400">📂 <?= $row['product_category'] ?></h4>
<div class="price">₹<?= number_format($row['product_price'],2) ?></div>
<div class="actions">
<button class="btn-cart add-cart" data-id="<?= $row['id'] ?>">🛒 Add to Cart</button>
<button class="remove-wish remove-wish-btn" data-id="<?= $row['wid'] ?>">🗑️ Remove</button>
</div></div>
<?php endwhile; ?></div>
<?php else: ?>
<div class="empty"><span>❤️</span><h2>Your wishlist is empty</h2><p>Save your favorite items here!</p>
<a href="user.php" class="continue">🛍️ Browse Products</a></div>
<?php endif; ?></div>
<script>
function filterWishlist(){
const q=document.getElementById('searchWish').value.toLowerCase();
let vis=0;
document.querySelectorAll('.wish-item').forEach(el=>{
const m=el.dataset.name.includes(q);el.style.display=m?'':'none';if(m)vis++});
document.getElementById('countBadge').textContent='('+vis+' items)';
}
filterWishlist();
document.querySelectorAll('.add-cart').forEach(b=>b.onclick=function(){
fetch('add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+this.dataset.id}).then(r=>r.json()).then(d=>{if(d.success)alert(d.message)})});
document.querySelectorAll('.remove-wish-btn').forEach(b=>b.onclick=function(){
if(confirm('Remove from wishlist?')) fetch('remove_from_wishlist.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+this.dataset.id}).then(r=>r.json()).then(d=>{if(d.success)location.reload()})});
</script>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

