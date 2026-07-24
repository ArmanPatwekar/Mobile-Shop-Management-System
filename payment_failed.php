<?php session_start(); ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>❌ Payment Failed | Phone Phactory</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}
.card{background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.1);max-width:450px;width:90%}
.icon{font-size:5em;margin-bottom:15px}
h1{color:#e94560;font-size:1.6em;margin-bottom:8px}
p{color:#666;line-height:1.6;margin-bottom:5px;font-size:.95em}
.actions{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;justify-content:center}
.actions a{padding:12px 25px;border-radius:25px;text-decoration:none;font-weight:600;transition:.3s}
.btn-primary{background:linear-gradient(135deg,#e94560,#0f3460);color:#fff}
.btn-secondary{background:#0f3460;color:#fff}
.btn-primary:hover,.btn-secondary:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,0,0,.2)}
.tips{text-align:left;background:#fff3f3;padding:15px;border-radius:10px;margin:15px 0}
.tips li{margin:5px 0;font-size:.9em;color:#666}
</style></head><body>
<div class="card">
<div class="icon">❌</div>
<h1>Payment Failed</h1>
<p>Sorry, your payment could not be processed. Please try again or use a different payment method.</p>
<ul class="tips">
<li>💳 Check your card details</li>
<li>💰 Ensure sufficient balance</li>
<li>🌐 Try a different network</li>
<li>📞 Contact your bank</li>
</ul>
<div class="actions">
<a href="checkout.php" class="btn-primary">🔄 Try Again</a>
<a href="cart.php" class="btn-secondary">🛒 Back to Cart</a>
</div></div></body></html>

