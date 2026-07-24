<?php
$conn =mysqli_connect("localhost", "root", "", "mobile_shop");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
}
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $product['product_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = "uploads/" . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) $image = $imageName;
    }
    $sql = "UPDATE products SET product_name='$name', product_category='$category', product_brand='$brand', product_price='$price', product_description='$description', product_image='$image' WHERE id=$id";
    if ($conn->query($sql) === TRUE) header("Location: index.php");
    else echo "Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product | Phone Phactory Admin Panel</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:linear-gradient(135deg,#f0f2f5,#e8ecf1);color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 15px rgba(0,0,0,.2)}
header .logo{font-size:1.2em;font-weight:700}
header nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
header nav a:hover{background:#e94560}
.form-wrapper{flex:1;display:flex;justify-content:center;align-items:center;padding:30px 20px}
form{background:#fff;padding:35px 30px;border-radius:15px;box-shadow:0 8px 30px rgba(0,0,0,.12);max-width:500px;width:100%;border-top:4px solid #0f3460}
h1{text-align:center;color:#1a1a2e;margin-bottom:5px;font-size:1.5em;display:flex;align-items:center;justify-content:center;gap:8px}
.desc-text{text-align:center;color:#888;font-size:.88em;margin-bottom:20px}
label{display:block;margin-bottom:4px;font-weight:600;color:#555;font-size:.88em}
input[type="text"],input[type="file"],textarea{width:100%;padding:11px 14px;margin:5px 0 14px;border:2px solid #e0e0e0;border-radius:8px;font-size:.95em;transition:.3s;font-family:inherit}
input:focus,textarea:focus{border-color:#0f3460;outline:none;box-shadow:0 0 0 3px rgba(15,52,96,.1)}
textarea{resize:vertical;min-height:70px}
img{display:block;margin:8px 0;border-radius:8px;border:2px solid #e0e0e0;padding:4px;background:#f8f8f8}
input[type="file"]{border:2px dashed #ccc;padding:8px;cursor:pointer;background:#fafafa}
button{width:100%;padding:13px;background:linear-gradient(135deg,#0f3460,#1a1a2e);color:#fff;border:none;border-radius:8px;font-size:1em;cursor:pointer;font-weight:600;transition:.3s;letter-spacing:.5px;margin-top:5px}
button:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(15,52,96,.4)}
.back-link{text-align:center;margin-top:15px;padding-top:12px;border-top:1px solid #f0f0f0}
.back-link a{color:#e94560;text-decoration:none;font-weight:600;font-size:.9em}
.back-link a:hover{text-decoration:underline}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto;font-size:.9em}
@media(max-width:480px){form{padding:25px 20px}}
</style>
</head>
<body>
<header>
<div class="logo">📱 PHONE PHACTORY • ADMIN</div>
<nav><a href="index.php">← Dashboard</a></nav>
</header>
<div class="form-wrapper">
<form action="edit.php" method="POST" enctype="multipart/form-data">
<h1>✏️ Edit Product</h1>
<p class="desc-text">Update the product information below and save changes to update inventory</p>
<input type="hidden" name="id" value="<?= $product['id'] ?>">
<label>Product Name</label>
<input type="text" name="name" value="<?= $product['product_name'] ?>" required>
<label>Product Category</label>
<input type="text" name="category" value="<?= $product['product_category'] ?>" required>
<label>Brand / Company</label>
<input type="text" name="brand" value="<?= htmlspecialchars($product['product_brand'] ?? 'General') ?>" required>
<label>Product Price</label>
<input type="text" name="price" value="<?= $product['product_price'] ?>" required>
<label>Product Description</label>
<textarea name="description" required><?= $product['product_description'] ?></textarea>
<label>Current Product Image</label>
<?php if ($product['product_image']): ?>
<img src="uploads/<?= $product['product_image'] ?>" alt="Current Product Image" width="120">
<?php endif; ?>
<label>Upload New Image (leave empty to keep current image)</label>
<input type="file" name="image">
<button type="submit" name="update">💾 Save Changes</button>
<div class="back-link"><a href="index.php">← Back to Admin Dashboard</a></div>
</form>
</div>
<footer>&copy; 2025 Phone Phactory Admin Panel</footer>
</body>
</html>
