<?php
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$brands_list = $conn->query("SELECT DISTINCT product_brand FROM products WHERE product_brand IS NOT NULL AND product_brand != '' ORDER BY product_brand");
$cats_list = $conn->query("SELECT DISTINCT product_category FROM products ORDER BY product_category");
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Dashboard | Phone Phactory</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700}nav{display:flex;gap:10px}
nav a,nav button{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s;border:none;cursor:pointer}
nav a:hover,nav button:hover{background:#e94560}
.hero-bar{background:linear-gradient(135deg,#0f3460,#e94560);color:#fff;text-align:center;padding:20px}
.hero-bar h1{font-size:1.5em}.hero-bar p{opacity:.9}
.container{padding:20px;max-width:1300px;margin:0 auto}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:12px;padding:18px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.stat-card .num{font-size:1.6em;font-weight:700;color:#0f3460}
.stat-card .label{color:#888;font-size:.85em}
.charts-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px}
@media(max-width:768px){.charts-grid{grid-template-columns:1fr}}
.chart-card{background:#fff;border-radius:12px;padding:18px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;flex-direction:column}
.chart-card h3{color:#1a1a2e;margin-bottom:10px;font-size:1em}
.chart-card canvas{width:100%!important;flex-shrink:0}
.period-select{float:right;padding:4px 10px;border-radius:6px;border:2px solid #e0e0e0;font-size:.85em}
.chart-footer{margin-top:12px;padding-top:12px;border-top:1px solid #eee}
.chart-footer .add-btn{display:block;padding:10px;background:linear-gradient(135deg,#e94560,#0f3460);color:#fff;text-decoration:none;border-radius:8px;font-weight:600;text-align:center;font-size:.9em;transition:.3s}
.chart-footer .add-btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(233,69,96,.3)}
.chart-footer .count-badge{display:block;background:#1a1a2e;color:#fff;padding:10px;border-radius:8px;font-size:.9em;font-weight:600;text-align:center}
.filter-group{margin-top:12px;padding-top:12px;border-top:1px solid #eee;display:flex;flex-direction:column;gap:8px}
.filter-group label{font-weight:600;color:#555;font-size:.82em}
.filter-group select{padding:8px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em;cursor:pointer;width:100%}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.08)}
th{background:#1a1a2e;color:#fff;padding:10px;font-size:.85em;text-transform:uppercase}
td{padding:10px;border-bottom:1px solid #eee;font-size:.9em;text-align:center;vertical-align:middle}
tr:hover{background:#f8f9ff}img{width:60px;height:60px;object-fit:contain;border-radius:8px;background:#f0f2f5;padding:4px}
.actions{display:flex;gap:6px;justify-content:center}
.actions a{padding:6px 14px;border-radius:6px;text-decoration:none;font-weight:600;font-size:.85em;transition:.3s}
.edit{background:#0f3460;color:#fff}.edit:hover{background:#1a1a2e}
.delete{background:#e94560;color:#fff}.delete:hover{background:#d63851}
.empty{text-align:center;padding:40px;color:#888}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:15px;margin-top:20px}
@media(max-width:768px){table{font-size:.8em}th,td{padding:8px 5px}img{width:40px;height:40px}}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY • ADMIN</div>
<nav><a href="add_product_form.php">+ Add Product</a><a href="admin_orders.php">📦 Orders</a><a href="sales_report.php">📈 Reports</a><form action="logout.php" method="POST" style="margin:0"><button type="submit">🚪 Logout</button></form></nav></header>
<section class="hero-bar"><h1>📊 Admin Dashboard</h1><p>Manage inventory, orders & view analytics</p></section>
<div class="container">
<div class="stats-grid" id="statsGrid"></div>

<!-- ROW 1: Sales Trend (left) + Category Distribution (right) -->
<div class="charts-grid">
  <div class="chart-card">
    <h3>📈 Sales Trend <select class="period-select" id="periodSelect" onchange="loadChart()"><option value="monthly">Monthly</option><option value="daily">Daily</option><option value="yearly">Yearly</option></select></h3>
    <canvas id="salesChart" height="160"></canvas>
    <div class="chart-footer"><a href="add_product_form.php" class="add-btn">➕ Add New Product</a></div>
  </div>
  <div class="chart-card">
    <h3>📂 Category Distribution</h3>
    <canvas id="categoryChart" height="160"></canvas>
    <div class="filter-group">
      <label>🔍 Filter by Brand</label>
      <select id="filterBrand" onchange="filterTable()">
        <option value="">All Brands</option>
        <?php while($b=$brands_list->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($b['product_brand']) ?>"><?= htmlspecialchars($b['product_brand']) ?></option>
        <?php endwhile; ?>
      </select>
      <label>🔍 Filter by Category</label>
      <select id="filterCategory" onchange="filterTable()">
        <option value="">All Categories</option>
        <?php while($c=$cats_list->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($c['product_category']) ?>"><?= htmlspecialchars($c['product_category']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
  </div>
  <div class="chart-card">
    <h3>🏷️ Brand Distribution</h3>
    <canvas id="brandChart" height="160"></canvas>
    <div class="chart-footer"><span class="count-badge" id="productCount">📦 Total: 0</span></div>
  </div>
</div>

<!-- PRODUCT TABLE -->
<table id="productTable"><thead><tr><th>#</th><th>Image</th><th>Name</th><th>Brand</th><th>Category</th><th>Price</th><th>Stock</th><th>Rating</th><th>Actions</th></tr></thead>
<?php $result2 = $conn->query("SELECT * FROM products"); $sn=1; if($result2->num_rows>0): while($row=$result2->fetch_assoc()): ?>
<tr class="product-row" data-brand="<?= htmlspecialchars($row['product_brand'] ?? '') ?>" data-category="<?= htmlspecialchars($row['product_category']) ?>">
<td><?= $sn++ ?></td><td><img src="uploads/<?= $row['product_image'] ?>" alt="<?= $row['product_name'] ?>"></td>
<td><?= $row['product_name'] ?></td>
<td><span style="background:#e94560;color:#fff;padding:3px 10px;border-radius:12px;font-size:.8em"><?= htmlspecialchars($row['product_brand'] ?? 'General') ?></span></td>
<td><?= $row['product_category'] ?></td><td>₹<?= number_format($row['product_price'],2) ?></td>
<td><?= $row['stock'] ?></td><td>⭐<?= $row['rating_avg'] ?></td>
<td class="actions"><a href="edit_form.php?id=<?= $row['id'] ?>" class="edit">✏️</a><a href="delete_product.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete?')">🗑️</a></td></tr>
<?php endwhile; else: ?><tr><td colspan="9" class="empty">No products found.</td></tr><?php endif; ?></tbody></table></div>
<script>
function getBrandColors(count) {
    const colors = ['#0f3460','#e94560','#ffc107','#4caf50','#9c27b0','#ff5722','#00bcd4','#795548','#607d8b','#3f51b5'];
    return colors.slice(0, count);
}
function filterTable(){
    const brand = document.getElementById('filterBrand').value.toLowerCase();
    const cat = document.getElementById('filterCategory').value.toLowerCase();
    document.querySelectorAll('.product-row').forEach(r => {
        const rb = r.dataset.brand.toLowerCase();
        const rc = r.dataset.category.toLowerCase();
        const matchBrand = !brand || rb === brand;
        const matchCat = !cat || rc === cat;
        r.style.display = (matchBrand && matchCat) ? '' : 'none';
    });
}
async function loadChart(){const p=document.getElementById('periodSelect').value;
const r=await fetch('admin_stats.php?period='+p);const d=await r.json();
if(d.sales){new Chart(document.getElementById('salesChart'),{type:'line',data:{labels:d.sales.map(s=>s.label),datasets:[{label:'Revenue (₹)',data:d.sales.map(s=>s.total),borderColor:'#e94560',backgroundColor:'rgba(233,69,96,.1)',fill:true,tension:.4}]},options:{responsive:true,plugins:{legend:{display:false}}}})}
if(d.categories){new Chart(document.getElementById('categoryChart'),{type:'doughnut',data:{labels:d.categories.map(c=>c.label),datasets:[{data:d.categories.map(c=>c.count),backgroundColor:['#0f3460','#e94560','#ffc107','#4caf50']}]},options:{responsive:true,plugins:{legend:{position:'bottom'}}}})}
if(d.brands){new Chart(document.getElementById('brandChart'),{type:'doughnut',data:{labels:d.brands.map(b=>b.label),datasets:[{data:d.brands.map(b=>b.count),backgroundColor:getBrandColors(d.brands.length)}]},options:{responsive:true,plugins:{legend:{position:'bottom'}}}})}}
async function loadStats(){const r=await fetch('admin_stats.php');const d=await r.json();
document.getElementById('statsGrid').innerHTML=
'<div class="stat-card"><div class="num">'+(d.total_products||0)+'</div><div class="label">📦 Products</div>'+
'<div class="stat-card"><div class="num">'+(d.total_orders||0)+'</div><div class="label">📋 Orders</div>'+
'<div class="stat-card"><div class="num">₹'+((d.total_revenue||0).toLocaleString())+'</div><div class="label">💰 Revenue</div>'+
'<div class="stat-card"><div class="num">'+(d.total_users||0)+'</div><div class="label">👥 Users</div>';
document.getElementById('productCount').textContent='📦 Total: '+(d.total_products||0)}
loadStats();loadChart();
</script>
<footer>&copy; 2025 Phone Phactory Admin Panel | <a href="home.html" style="color:#e94560;text-decoration:none">Visit Site</a></footer></body></html>
