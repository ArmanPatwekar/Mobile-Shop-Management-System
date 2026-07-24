<?php session_start();
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) die("Connection failed");
$user_id = $_SESSION['user_id'] ?? 0;
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$categories = $conn->query("SELECT DISTINCT product_category FROM products");
$cart_count = 0;
if ($user_id) {
    $cc = $conn->query("SELECT SUM(quantity) as c FROM cart WHERE user_id=$user_id");
    $cart_count = $cc->fetch_assoc()['c'] ?? 0;
    $wish_ids = $conn->query("SELECT product_id FROM wishlist WHERE user_id=$user_id");
    $wish_arr = [];
    while ($w = $wish_ids->fetch_assoc()) $wish_arr[] = $w['product_id'];
}
require_once 'recommendations.php';
$recs = $user_id ? getRecommendations($conn, $user_id) : $conn->query("SELECT * FROM products ORDER BY rating_avg DESC LIMIT 4");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>🛍️ Shop | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700;display:flex;align-items:center;gap:6px}
nav{display:flex;align-items:center;gap:8px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s;display:flex;align-items:center;gap:4px}
nav a:hover{background:#e94560}
.badge{background:#e94560;color:#fff;border-radius:50%;padding:2px 8px;font-size:.75em;font-weight:700}
.hero-bar{background:linear-gradient(135deg,#0f3460,#e94560);color:#fff;text-align:center;padding:25px 20px}
.hero-bar h1{font-size:1.6em;margin-bottom:4px}
.hero-bar p{opacity:.9;font-size:.95em}
.search-bar{display:flex;justify-content:center;gap:8px;padding:15px 20px 0;flex-wrap:wrap}
.search-bar input{padding:10px 16px;border:2px solid #e0e0e0;border-radius:25px;font-size:.95em;width:300px;max-width:100%;transition:.3s}
.search-bar input:focus{border-color:#0f3460;outline:none}
.search-bar select{padding:10px 16px;border:2px solid #e0e0e0;border-radius:25px;font-size:.95em;background:#fff;cursor:pointer}
.search-bar button{padding:10px 22px;background:#0f3460;color:#fff;border:none;border-radius:25px;cursor:pointer;font-weight:600;transition:.3s}
.search-bar button:hover{background:#e94560}
.container{flex:1;max-width:1200px;margin:20px auto;padding:0 20px;width:100%}
.section-title{font-size:1.3em;color:#1a1a2e;margin:20px 0 15px;display:flex;align-items:center;gap:8px}
.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;margin-bottom:30px}
.product-card{background:#fff;border-radius:15px;padding:18px;text-align:center;box-shadow:0 4px 15px rgba(0,0,0,.08);transition:.4s;position:relative}
.product-card:hover{transform:translateY(-6px);box-shadow:0 12px 30px rgba(0,0,0,.15)}
.product-card img{width:100%;height:150px;object-fit:contain;border-radius:10px;margin-bottom:10px;background:#f8f8f8;padding:8px}
.product-card h3{font-size:1em;color:#1a1a2e;margin:5px 0 3px}
.product-card h4{font-size:.82em;color:#888;font-weight:400}
.product-card .stars{color:#ffc107;font-size:.85em;margin:3px 0}
.product-card .price{color:#e94560;font-size:1.2em;font-weight:700;margin:6px 0 10px}
.product-card .actions{display:flex;gap:6px}
.product-card .actions button{flex:1;padding:8px 0;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:.85em;transition:.3s}
.btn-cart{background:#0f3460;color:#fff}.btn-cart:hover{background:#1a1a2e;transform:translateY(-1px)}
.btn-wish{background:#f0f2f5;color:#e94560;font-size:1.2em;padding:4px!important;min-width:40px;display:flex;align-items:center;justify-content:center}
.btn-wish.active{background:#e94560;color:#fff}
.btn-view{background:#e94560;color:#fff}.btn-view:hover{background:#d63851}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
.empty{text-align:center;padding:40px;color:#888}
@media(max-width:600px){.product-grid{grid-template-columns:repeat(auto-fill,minmax(170px,1fr))}.search-bar input{width:100%}}
</style></head><body>
<header><div class="logo">📱 Phone Phactory</div>
<nav><a href="home.html">🏠 Home</a><a href="cart.php">🛒 Cart<?php if($cart_count>0): ?><span class="badge"><?= $cart_count ?></span><?php endif; ?></a>
<a href="wishlist.php">❤️ Wishlist</a><a href="profile.php">👤 Profile</a><a href="logout.php">🚪 Logout</a></nav></header>
<section class="hero-bar"><h1>🛍️ Our Product Collection</h1><p>Browse the latest mobiles, accessories & smartwatches at best prices</p></section>
<div class="search-bar"><input type="text" id="searchInput" placeholder="🔍 Search products..." onkeyup="searchProducts()">
<select id="catFilter" onchange="searchProducts()"><option value="">All Categories</option>
<?php while($c=$categories->fetch_assoc()): ?><option value="<?= $c['product_category'] ?>"><?= $c['product_category'] ?></option>
<?php endwhile; ?></select><button onclick="searchProducts()">🔍 Search</button></div>
<div class="container">
<?php if($user_id && $recs->num_rows>0): ?>
<h2 class="section-title">🤖 Recommended For You</h2>
<div class="product-grid" id="recGrid">
<?php while($r=$recs->fetch_assoc()): $in_w = in_array($r['id'], $wish_arr ?? []); ?>
<div class="product-card">
<a href="product_details.php?id=<?= $r['id'] ?>" style="text-decoration:none;color:inherit">
<img src="uploads/<?= $r['product_image'] ?>" alt="<?= $r['product_name'] ?>">
<h3><?= $r['product_name'] ?></h3>
<h4>📂 <?= $r['product_category'] ?></h4>
<div class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=$r['rating_avg']?'⭐':'☆'; ?></div>
<div class="price">₹<?= number_format($r['product_price'],2) ?></div></a>
<div class="actions">
<button class="btn-cart" onclick="addToCart(<?= $r['id'] ?>)">🛒</button>
<button class="btn-wish <?= $in_w?'active':'' ?>" onclick="toggleWish(<?= $r['id'] ?>,this)"><?= $in_w?'❤️':'🤍' ?></button>
<button class="btn-view" onclick="location.href='product_details.php?id=<?= $r['id'] ?>'">👁️</button></div></div>
<?php endwhile; ?></div><hr style="margin:10px 0 20px;border-color:#eee"><?php endif; ?>
<h2 class="section-title">📦 All Products <span id="resultCount" style="color:#888;font-size:.7em"></span></h2>
<div class="product-grid" id="productGrid">
<?php while($row=mysqli_fetch_assoc($result)): $in_w = in_array($row['id'], $wish_arr ?? []); ?>
<div class="product-card">
<a href="product_details.php?id=<?= $row['id'] ?>" style="text-decoration:none;color:inherit">
<img src="uploads/<?= $row['product_image'] ?>" alt="<?= $row['product_name'] ?>">
<h3><?= $row['product_name'] ?></h3>
<h4>📂 <?= $row['product_category'] ?></h4>
<div class="stars"><?php for($i=1;$i<=5;$i++) echo $i<=$row['rating_avg']?'⭐':'☆'; ?></div>
<div class="price">₹<?= number_format($row['product_price'],2) ?></div></a>
<div class="actions">
<button class="btn-cart" onclick="addToCart(<?= $row['id'] ?>)">🛒 Add</button>
<button class="btn-wish <?= $in_w?'active':'' ?>" onclick="toggleWish(<?= $row['id'] ?>,this)"><?= $in_w?'❤️':'🤍' ?></button>
<button class="btn-view" onclick="location.href='product_details.php?id=<?= $row['id'] ?>'">👁️</button></div></div>
<?php endwhile; ?></div></div>
<script>
function addToCart(id){fetch('add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){alert(d.message);location.reload()}else{alert('Please login first');window.location='login.html'}})}
function toggleWish(id,el){fetch('add_to_wishlist.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'product_id='+id}).then(r=>r.json()).then(d=>{if(d.success){if(d.action==='added'){el.classList.add('active');el.innerHTML='❤️'}else{el.classList.remove('active');el.innerHTML='🤍'}}})}
async function searchProducts(){const q=document.getElementById('searchInput').value,cat=document.getElementById('catFilter').value;
const r=await fetch('search_products.php?q='+encodeURIComponent(q)+'&cat='+encodeURIComponent(cat));const d=await r.json();
const grid=document.getElementById('productGrid');grid.innerHTML='';
document.getElementById('resultCount').textContent='('+d.length+' results)';
d.forEach(p=>{grid.innerHTML+=`<div class="product-card"><a href="product_details.php?id=${p.id}" style="text-decoration:none;color:inherit"><img src="uploads/${p.image}" alt="${p.name}"><h3>${p.name}</h3><h4>📂 ${p.category}</h4><div class="stars">${'⭐'.repeat(Math.round(p.rating||0))}${'☆'.repeat(5-Math.round(p.rating||0))}</div><div class="price">₹${parseFloat(p.price).toLocaleString()}</div></a><div class="actions"><button class="btn-cart" onclick="addToCart(${p.id})">🛒 Add</button><button class="btn-wish" onclick="toggleWish(${p.id},this)">🤍</button><button class="btn-view" onclick="location.href='product_details.php?id=${p.id}'">👁️</button></div></div>`})}
</script>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

