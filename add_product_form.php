 <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product | Phone Phactory Admin Panel</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 15px rgba(0,0,0,.2)}
header .logo{font-size:1.2em;font-weight:700}
header nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
header nav a:hover{background:#e94560}
.form-wrapper{flex:1;display:flex;justify-content:center;align-items:center;padding:20px;min-height:0}
form{background:#fff;padding:30px 28px;border-radius:15px;box-shadow:0 8px 30px rgba(0,0,0,.12);max-width:480px;width:100%;border-top:4px solid #0f3460;margin:auto}
.form-header{background:linear-gradient(135deg,#0f3460,#16213e);margin:-30px -28px 22px -28px;padding:28px 20px;border-radius:15px 15px 0 0;text-align:center;border-bottom:3px solid #e94560}
h2{text-align:center;color:#ffffff;margin-bottom:6px;font-size:1.6em;display:flex;align-items:center;justify-content:center;gap:8px;font-weight:700}
.sub-text{text-align:center;color:rgba(255,255,255,.75);font-size:.9em;margin-bottom:0;letter-spacing:.3px}
label{display:block;margin-bottom:4px;font-weight:600;color:#555;font-size:.88em}
input[type="text"],input[type="number"],input[type="file"],textarea{width:100%;padding:11px 14px;margin:5px 0 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.95em;transition:.3s;font-family:inherit}
input:focus,textarea:focus{border-color:#0f3460;outline:none;box-shadow:0 0 0 3px rgba(15,52,96,.1)}
textarea{resize:vertical;min-height:75px;font-family:inherit}
input[type="file"]{border:2px dashed #ccc;padding:8px;cursor:pointer;background:#fafafa}
.variant-row{display:flex;gap:8px;margin-bottom:8px;align-items:center}
.variant-row input[type="text"]{flex:2;padding:8px;border:2px solid #e0e0e0;border-radius:6px;font-size:.9em;margin:0}
.variant-row input[type="number"]{width:70px;padding:8px;border:2px solid #e0e0e0;border-radius:6px;font-size:.9em;margin:0}
.variant-row .remove-color{background:#e94560;color:#fff;border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1em;line-height:1}
.btn-variant{padding:8px 16px;background:#0f3460;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:.85em;margin-bottom:14px}
button{width:100%;padding:13px;background:linear-gradient(135deg,#0f3460,#1a1a2e);color:#fff;border:none;border-radius:8px;font-size:1em;cursor:pointer;font-weight:600;transition:.3s;letter-spacing:.5px;margin-top:5px}
button:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(15,52,96,.4)}
.back-link{display:block;text-align:center;margin-top:15px;padding-top:12px;border-top:1px solid #f0f0f0}
.back-link a{color:#e94560;text-decoration:none;font-weight:600;font-size:.9em}
.back-link a:hover{text-decoration:underline}
.required{color:#e94560;font-size:.8em}
@media(max-width:480px){form{padding:25px 20px}}
</style>
</head>
<body>
<header>
<div class="logo">📱 PHONE PHACTORY • ADMIN</div>
<nav><a href="index.php">← Return to Dashboard</a></nav>
</header>
<div class="form-wrapper">
<form action="add_product.php" method="POST" enctype="multipart/form-data">
<div class="form-header">
<h2>➕ Add New Product</h2>
<p class="sub-text">Fill in the product details below to add to inventory system</p>
</div>
<label>Product Name <span class="required">*</span></label>
<input type="text" name="product_name" placeholder="Enter full product name" required>
<label>Product Category <span class="required">*</span></label>
<input type="text" name="product_category" placeholder="e.g. Smartphones, Accessories, Watches" required>
<label>Brand / Company <span class="required">*</span></label>
<select name="product_brand" id="brandSelect" style="width:100%;padding:11px 14px;margin:5px 0 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.95em;font-family:inherit" required onchange="toggleCustomBrand()">
<option value="">-- Select Brand --</option>
<option value="Apple">Apple</option>
<option value="Samsung">Samsung</option>
<option value="OnePlus">OnePlus</option>
<option value="Xiaomi">Xiaomi</option>
<option value="Sony">Sony</option>
<option value="Other">Other (type below)</option>
</select>
<input type="text" name="product_brand_custom" id="customBrandInput" placeholder="Enter brand name" style="display:none;width:100%;padding:11px 14px;margin:5px 0 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.95em;font-family:inherit">
<script>
function toggleCustomBrand() {
    var sel = document.getElementById('brandSelect');
    var custom = document.getElementById('customBrandInput');
    if (sel.value === 'Other') {
        custom.style.display = 'block';
        custom.required = true;
    } else {
        custom.style.display = 'none';
        custom.required = false;
    }
}
</script>
<label>Product Price (₹) <span class="required">*</span></label>
<input type="number" name="product_price" step="0.01" placeholder="0.00" required>
<label>Product Description <span class="required">*</span></label>
<textarea name="product_description" placeholder="Describe product features, specifications, condition, color options, warranty information, and any other relevant details." required></textarea>

<label>🎨 Price Variants by Color</label>
<div id="colorVariants">
<div class="variant-row">
<input type="text" name="color_names[]" placeholder="Color name (e.g. Midnight Black)" class="v-color">
<input type="text" name="color_price[]" placeholder="Override price" class="v-price">
<input type="number" name="color_stock[]" placeholder="Stock" class="v-stock" value="10">
<button type="button" class="remove-color" onclick="this.parentElement.remove()">✕</button>
</div>
<button type="button" class="btn-variant" onclick="addColorVariant()">+ Add Another Color</button>
<script>
function addColorVariant(){
    var div = document.getElementById('colorVariants');
    var row = document.createElement('div');
    row.className = 'variant-row';
    row.innerHTML = '<input type="text" name="color_names[]" placeholder="Color name" class="v-color">' +
    '<input type="text" name="color_price[]" placeholder="Override price" class="v-price">' +
    '<input type="number" name="color_stock[]" placeholder="Stock" class="v-stock" value="10">' +
    '<button type="button" class="remove-color" onclick="this.parentElement.remove()">✕</button>';
    div.appendChild(row);
}
</script>

<label>Product Image <span class="required">*</span></label>
<input type="file" name="product_image" accept="image/*" required>
<button type="submit">📦 Add Product to Inventory System</button>
<div class="back-link"><a href="index.php">← Back to Admin Dashboard</a></div>
</form>
    </div>
</body>
</html>

