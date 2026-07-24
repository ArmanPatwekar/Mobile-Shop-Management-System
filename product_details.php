<?php session_start();
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$id = intval($_GET['id']);
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) { header("Location: user.php"); exit; }
$reviews = $conn->query("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id=u.id WHERE r.product_id=$id ORDER BY r.created_at DESC");
$avg = $conn->query("SELECT AVG(rating) as avg FROM reviews WHERE product_id=$id")->fetch_assoc()['avg'] ?? 0;
$avg = round($avg, 1);
$in_wishlist = isset($_SESSION['user_id']) ? $conn->query("SELECT id FROM wishlist WHERE user_id={$_SESSION['user_id']} AND product_id=$id")->num_rows > 0 : false;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= $product['product_name'] ?> | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700}nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
nav a:hover{background:#e94560}.container{flex:1;max-width:1000px;margin:30px auto;padding:0 20px;width:100%}
.product-detail{display:flex;gap:30px;background:#fff;border-radius:15px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,.08);flex-wrap:wrap}
.product-detail img{width:300px;height:300px;object-fit:contain;border-radius:10px;background:#f8f8f8;padding:10px}
.info{flex:1;min-width:250px}
.info h1{color:#1a1a2e;font-size:1.5em}
.info .cat{color:#888;font-size:.9em;margin:5px 0}
.info .price{color:#e94560;font-size:1.8em;font-weight:700;margin:10px 0}
.info .desc{color:#555;line-height:1.6;font-size:.95em;margin:10px 0}
.stars{color:#ffc107;font-size:1.2em;margin:5px 0}
.actions{display:flex;gap:10px;margin:15px 0;flex-wrap:wrap}
.actions button{padding:12px 25px;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:.3s;font-size:.95em}
.btn-cart{background:#0f3460;color:#fff}
.btn-cart:hover{background:#1a1a2e;transform:translateY(-2px)}
.btn-wish{background:#e94560;color:#fff}
.btn-wish:hover{background:#d63851;transform:translateY(-2px)}
.btn-buy{background:linear-gradient(135deg,#e94560,#0f3460);color:#fff}
.btn-buy:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(233,69,96,.4)}
.reviews{margin-top:30px;background:#fff;border-radius:15px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,.08)}
.reviews h2{margin-bottom:15px;color:#1a1a2e}
.review-item{border-bottom:1px solid #eee;padding:12px 0}
.review-item:last-child{border-bottom:none}
.review-item .user{font-weight:600;color:#0f3460}
.review-item .date{font-size:.8em;color:#aaa;float:right}
.review-item p{margin-top:5px;color:#555;font-size:.9em}
.review-form textarea{width:100%;padding:10px;border:2px solid #e0e0e0;border-radius:8px;margin:10px 0;font-family:inherit;resize:vertical;min-height:80px}
.review-form select{padding:8px;border-radius:6px;border:2px solid #e0e0e0}
.review-form button{padding:10px 25px;background:#0f3460;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
@media(max-width:768px){.product-detail img{width:100%;height:auto;max-height:250px}}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="user.php">🛍️ Shop</a><a href="cart.php">🛒 Cart</a><?php if(isset($_SESSION['user_id'])): ?><a href="logout.php">🚪 Logout</a><?php else: ?><a href="login.html">🔐 Login</a><?php endif; ?></nav></header>
<div class="container">
<div class="product-detail">
<img src="uploads/<?= $product['product_image'] ?>" alt="<?= $product['product_name'] ?>">
<div class="info">
<h1><?= $product['product_name'] ?></h1>
<p class="cat">📂 <?= $product['product_category'] ?> | 📦 Stock: <?= $product['stock'] ?></p>
<div class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=$avg ? '⭐' : '☆'; ?> <span style="color:#888;font-size:.8em">(<?= $avg ?>)</span></div>
<div class="price">₹<?= number_format($product['product_price'],2) ?></div>
<p class="desc"><?= $product['product_description'] ?></p>
<div class="actions">
<button class="btn-cart" onclick="addToCart(<?= $product['id'] ?>)">🛒 Add to Cart</button>
<button class="btn-wish" onclick="toggleWish(<?= $product['id'] ?>)"><?= $in_wishlist ? '❤️' : '🤍' ?> Wishlist</button>
<button class="btn-buy" onclick="buyNow(<?= $product['id'] ?>)">⚡ Buy Now</button>
</div></div></div>
<div class="reviews">
<h2>⭐ Reviews & Ratings</h2>
<?php if(isset($_SESSION['user_id'])): ?>
<form class="review-form" id="reviewForm" onsubmit="return submitReview(event)">
<input type="hidden" name="product_id" value="<?= $id ?>">
<select name="rating" id="reviewRating" required><option value="">Rating</option>
<option value="5">⭐⭐⭐⭐⭐ 5</option><option value="4">⭐⭐⭐⭐ 4</option>
<option value="3">⭐⭐⭐ 3</option><option value="2">⭐⭐ 2</option><option value="1">⭐ 1</option></select>
<textarea name="review_text" id="reviewText" placeholder="Share your experience with this product..." required></textarea>
<button type="submit" id="reviewBtn">📝 Submit Review</button>
<span id="reviewMsg" style="margin-left:10px;font-weight:600"></span></form>
<script>
async function submitReview(e){e.preventDefault();
const btn=document.getElementById('reviewBtn');const msg=document.getElementById('reviewMsg');
btn.textContent='⏳ Submitting...';btn.disabled=true;msg.textContent='';
const fd=new FormData(e.target);
const r=await fetch('submit_review.php',{method:'POST',body:fd});const d=await r.json();
if(d.success){msg.style.color='#2e7d32';msg.textContent='✅ Review submitted!';btn.textContent='📝 Submitted';
setTimeout(()=>location.reload(),1500)}
else{msg.style.color='#c62828';msg.textContent='❌ Error submitting review';btn.textContent='📝 Submit Review';btn.disabled=false}
return false;}
</script>
<?php else: ?><p><a href="login.html" style="color:#e94560">Login</a> to write a review.</p>
<?php endif; ?>
<?php if($reviews->num_rows>0): while($rev=$reviews->fetch_assoc()): ?>
<div class="review-item">
<span class="user"><?= $rev['name'] ?></span>
<span class="date"><?= date('d M Y',strtotime($rev['created_at'])) ?></span>
<div class="stars" style="font-size:1em"><?php for($i=1;$i<=5;$i++) echo $i<=$rev['rating']?'⭐':'☆'; ?></div>
<p><?= $rev['review_text'] ?></p></div>
<?php endwhile; else: ?><p style="color:#888">No reviews yet. Be the first to review!</p>
<?php endif; ?></div></div>
<script>
function addToCart(id){fetch('add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+id}).then(r=>r.json()).then(d=>d.success?alert(d.message):alert('Login first'))}
function toggleWish(id){fetch('add_to_wishlist.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+id}).then(r=>r.json()).then(d=>{if(d.success)location.reload()})}
function buyNow(id){fetch('add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+id}).then(r=>r.json()).then(d=>{if(d.success)window.location='checkout.php'})}
</script>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

