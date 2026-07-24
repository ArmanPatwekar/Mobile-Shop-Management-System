<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$user = $conn->query("SELECT * FROM users WHERE id={$_SESSION['user_id']}")->fetch_assoc();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>👤 My Profile | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700}nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
nav a:hover{background:#e94560}.container{flex:1;max-width:600px;margin:30px auto;padding:0 20px;width:100%}
h1{color:#1a1a2e;margin-bottom:20px}.card{background:#fff;border-radius:15px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,.08);margin-bottom:20px}
.avatar{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0f3460,#e94560);color:#fff;display:flex;align-items:center;justify-content:center;font-size:2.5em;margin:0 auto 15px}
.form-group{margin-bottom:14px}
.form-group label{display:block;margin-bottom:3px;font-weight:600;color:#555;font-size:.85em}
.form-group input,.form-group textarea{width:100%;padding:10px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em;transition:.3s;font-family:inherit}
.form-group input:focus,.form-group textarea:focus{border-color:#0f3460;outline:none}
.form-group textarea{resize:vertical;min-height:60px}
.btn{width:100%;padding:12px;background:linear-gradient(135deg,#0f3460,#1a1a2e);color:#fff;border:none;border-radius:8px;font-size:1em;font-weight:600;cursor:pointer;transition:.3s}
.btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(15,52,96,.4)}
.links{display:flex;gap:10px;margin-top:15px;flex-wrap:wrap}
.links a{flex:1;padding:10px;text-align:center;background:#f0f2f5;color:#1a1a2e;text-decoration:none;border-radius:8px;font-weight:600;font-size:.9em;transition:.3s;min-width:120px}
.links a:hover{background:#e94560;color:#fff}
.success{background:#e8f5e9;color:#2e7d32;padding:10px;border-radius:8px;margin-bottom:15px;text-align:center;font-weight:600}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div>
<nav><a href="user.php">🛍️ Shop</a><a href="my_orders.php">📋 Orders</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container"><h1>👤 My Profile</h1>
<?php if(isset($_GET['updated'])): ?><div class="success">✅ Profile updated successfully!</div><?php endif; ?>
<div class="card">
<div class="avatar"><?= strtoupper(substr($user['name'],0,1)) ?></div>
<form action="update_profile.php" method="POST">
<div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= $user['name'] ?>" required></div>
<div class="form-group"><label>Mobile</label><input type="text" name="mobile" value="<?= $user['mobile'] ?>" required></div>
<div class="form-group"><label>Email</label><input type="email" name="email" value="<?= $user['email'] ?>" required></div>
<div class="form-group"><label>Address</label><textarea name="address"><?= $user['address'] ?? '' ?></textarea></div>
<button type="submit" class="btn">💾 Save Changes</button></form></div>
<div class="links">
<a href="change_password.php">🔐 Change Password</a>
<a href="my_orders.php">📦 My Orders</a>
<a href="wishlist.php">❤️ Wishlist</a>
<a href="user.php">🛍️ Shop</a></div></div>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

