<?php session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $user = $conn->query("SELECT password FROM users WHERE id=$user_id")->fetch_assoc();
    if (!password_verify($old, $user['password'])) {
        $error = "❌ Current password is incorrect";
    } elseif ($new !== $confirm) {
        $error = "❌ New passwords do not match";
    } elseif (strlen($new) < 6) {
        $error = "❌ Password must be at least 6 characters";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hash' WHERE id=$user_id");
        $success = "✅ Password changed successfully!";
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>🔐 Change Password | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center}
.logo{font-size:1.2em;font-weight:700}nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;background:rgba(255,255,255,.1);transition:.3s}nav a:hover{background:#e94560}
.container{flex:1;max-width:450px;margin:40px auto;padding:0 20px;width:100%}
.card{background:#fff;border-radius:15px;padding:30px;box-shadow:0 4px 15px rgba(0,0,0,.08)}
h1{color:#1a1a2e;margin-bottom:20px;text-align:center;font-size:1.4em}
.form-group{margin-bottom:14px}
.form-group label{display:block;margin-bottom:3px;font-weight:600;color:#555;font-size:.85em}
.form-group input{width:100%;padding:10px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em;transition:.3s}
.form-group input:focus{border-color:#0f3460;outline:none}
.btn{width:100%;padding:12px;background:linear-gradient(135deg,#e94560,#0f3460);color:#fff;border:none;border-radius:8px;font-size:1em;font-weight:600;cursor:pointer;transition:.3s}
.btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(233,69,96,.4)}
.back{display:block;text-align:center;margin-top:15px;color:#e94560;text-decoration:none;font-weight:600}
.msg{padding:10px;border-radius:8px;margin-bottom:15px;text-align:center;font-weight:600}
.success{background:#e8f5e9;color:#2e7d32}.error{background:#ffebee;color:#c62828}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:auto}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY</div><nav><a href="profile.php">← Profile</a></nav></header>
<div class="container"><div class="card">
<h1>🔐 Change Password</h1>
<?php if(isset($success)): ?><div class="msg success"><?= $success ?></div><?php endif; ?>
<?php if(isset($error)): ?><div class="msg error"><?= $error ?></div><?php endif; ?>
<form method="POST"><div class="form-group"><label>Current Password</label>
<input type="password" name="old_password" required></div>
<div class="form-group"><label>New Password</label>
<input type="password" name="new_password" minlength="6" required></div>
<div class="form-group"><label>Confirm New Password</label>
<input type="password" name="confirm_password" minlength="6" required></div>
<button type="submit" class="btn">🔄 Update Password</button></form>
<a href="profile.php" class="back">← Back to Profile</a></div></div>
<footer>&copy; 2025 Phone Phactory. All rights reserved.</footer></body></html>

