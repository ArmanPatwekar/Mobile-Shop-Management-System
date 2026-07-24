<?php
// Simple email notification function using PHP mail()
function sendOrderEmail($to, $name, $order_id, $total) {
    $subject = "📦 Order Confirmation - Phone Phactory #$order_id";
    $message = "
    <html><body style='font-family:Arial,sans-serif;background:#f4f4f4;padding:20px'>
    <div style='max-width:600px;margin:auto;background:#fff;border-radius:10px;padding:25px;box-shadow:0 4px 10px rgba(0,0,0,.1)'>
    <div style='text-align:center;border-bottom:2px solid #e94560;padding-bottom:15px;margin-bottom:20px'>
    <h1 style='color:#1a1a2e;margin:0'>📱 Phone Phactory</h1>
    <p style='color:#e94560;font-weight:700'>Premium Mobile Store</p></div>
    <h2 style='color:#1a1a2e'>✅ Order Confirmed!</h2>
    <p>Dear <strong>$name</strong>,</p>
    <p>Your order has been placed successfully!</p>
    <div style='background:#f8f9ff;padding:15px;border-radius:8px;margin:15px 0'>
    <p style='margin:5px 0'><strong>Order #:</strong> $order_id</p>
    <p style='margin:5px 0'><strong>Total:</strong> ₹" . number_format($total,2) . "</p>
    <p style='margin:5px 0'><strong>Status:</strong> Processing</p></div>
    <p>Estimated delivery: 3-5 business days.</p>
    <a href='http://localhost/Project/order_details.php?id=$order_id'
    style='display:inline-block;padding:10px 25px;background:linear-gradient(135deg,#e94560,#0f3460);color:#fff;
    text-decoration:none;border-radius:25px;margin:10px 0'>📋 View Order</a>
    <p style='color:#888;font-size:.85em;margin-top:20px;border-top:1px solid #eee;padding-top:15px'>
    Thank you for shopping with us!<br>📱 Phone Phactory | 📞 +91-9876543210</p></div></body></html>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: support@phonephactory.com\r\n";
    return mail($to, $subject, $message, $headers);
}
function sendWelcomeEmail($to, $name) {
    $subject = "🎉 Welcome to Phone Phactory!";
    $message = "
    <html><body style='font-family:Arial,sans-serif;background:#f4f4f4;padding:20px'>
    <div style='max-width:600px;margin:auto;background:#fff;border-radius:10px;padding:25px;box-shadow:0 4px 10px rgba(0,0,0,.1)'>
    <h1 style='color:#1a1a2e;text-align:center'>📱 Phone Phactory</h1>
    <h2 style='color:#e94560;text-align:center'>Welcome, $name! 🎉</h2>
    <p>Thank you for creating an account! Start shopping for the latest mobile phones and accessories.</p>
    <a href='http://localhost/Project/user.php'
    style='display:inline-block;padding:10px 25px;background:linear-gradient(135deg,#e94560,#0f3460);color:#fff;
    text-decoration:none;border-radius:25px;margin:10px 0'>🛍️ Start Shopping</a>
    <p style='color:#888;font-size:.85em;margin-top:20px'>Need help? Contact us at support@phonephactory.com</p></div></body></html>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: support@phonephactory.com\r\n";
    return mail($to, $subject, $message, $headers);
}
?>
