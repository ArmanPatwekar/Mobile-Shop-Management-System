d<?php session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: Adminlogin.html"); exit; }
$conn = mysqli_connect('localhost', 'root', '', 'mobile_shop');
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
// Get daily sales data for chart
$chart_data = $conn->query("SELECT DATE(order_date) as date, SUM(total_amount) as total FROM orders WHERE DATE(order_date) BETWEEN '$from' AND '$to' AND status!='cancelled' GROUP BY DATE(order_date) ORDER BY date ASC");
$chart_labels = []; $chart_values = [];
while($r = $chart_data->fetch_assoc()) {
    $chart_labels[] = date('d M', strtotime($r['date']));
    $chart_values[] = floatval($r['total']);
}
$orders = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id WHERE DATE(o.order_date) BETWEEN '$from' AND '$to' ORDER BY o.order_date DESC");
$count = $orders->num_rows;
$result2 = $conn->query("SELECT SUM(total_amount) as t FROM orders WHERE DATE(order_date) BETWEEN '$from' AND '$to' AND status!='cancelled'");
$total_rev = $result2->fetch_assoc()['t'] ?? 0;
$avg = $count > 0 ? $total_rev / $count : 0;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>📈 Sales Reports | Admin</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Arial,sans-serif;background:url('background.png') no-repeat center center fixed;background-size:cover;color:#333}
header{background:linear-gradient(135deg,#1a1a2e,#16213e);color:#fff;padding:14px 25px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100}
.logo{font-size:1.2em;font-weight:700}nav{display:flex;gap:10px}
nav a{color:#fff;text-decoration:none;padding:7px 16px;border-radius:20px;font-size:.9em;background:rgba(255,255,255,.1);transition:.3s}
nav a:hover{background:#e94560}.container{padding:25px;max-width:1200px;margin:0 auto}
h1{color:#1a1a2e;margin-bottom:20px}.filter-card{background:#fff;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;gap:15px;align-items:end;flex-wrap:wrap}
.filter-card label{font-weight:600;color:#555;font-size:.85em;display:block;margin-bottom:3px}
.filter-card input{padding:8px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:.9em}
.filter-card button{padding:9px 22px;background:#0f3460;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:.3s}
.filter-card button:hover{background:#e94560}.export-btn{background:#e94560!important}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:12px;padding:18px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.stat-card .num{font-size:1.8em;font-weight:700;color:#0f3460}
.stat-card .label{color:#888;font-size:.85em;margin-top:3px}
.chart-wrapper{background:#fff;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.chart-wrapper h3{color:#1a1a2e;margin-bottom:12px;font-size:1.1em}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.08)}
th{background:#1a1a2e;color:#fff;padding:12px 10px;font-size:.85em;text-transform:uppercase}
td{padding:10px;border-bottom:1px solid #eee;font-size:.9em;text-align:center}
tr:hover{background:#f8f9ff}
footer{background:#1a1a2e;color:#fff;text-align:center;padding:18px;margin-top:20px}
</style></head><body>
<header><div class="logo">📱 PHONE PHACTORY • ADMIN</div>
<nav><a href="index.php">📊 Dashboard</a><a href="admin_orders.php">📦 Orders</a><a href="logout.php">🚪 Logout</a></nav></header>
<div class="container"><h1>📈 Sales Reports</h1>
<form class="filter-card" method="GET">
<div><label>From Date</label><input type="date" name="from" value="<?= $from ?>"></div>
<div><label>To Date</label><input type="date" name="to" value="<?= $to ?>"></div>
<div><label>&nbsp;</label><button type="submit">🔍 Filter</button>
<button type="button" class="export-btn" onclick="exportCSV()">📥 Export CSV</button></div></form>
<div class="stats">
<div class="stat-card"><div class="num"><?= $count ?></div><div class="label">📦 Total Orders</div>
<div class="stat-card"><div class="num">₹<?= number_format($total_rev,2) ?></div><div class="label">💰 Total Revenue</div>
<div class="stat-card"><div class="num">₹<?= number_format($avg,2) ?></div><div class="label">📊 Avg Order Value</div>
</div>
<?php if (!empty($chart_labels)): ?>
<div class="chart-wrapper">
<h3>📈 Sales Trend (Daily)</h3>
<canvas id="salesLineChart" height="120"></canvas>
</div>
<script>
new Chart(document.getElementById('salesLineChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?= json_encode($chart_values) ?>,
            borderColor: '#e94560',
            backgroundColor: 'rgba(233,69,96,.1)',
            fill: true,
            tension: .4,
            pointBackgroundColor: '#e94560',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => '₹' + v.toLocaleString() }
            }
        }
    }
});
</script>
<?php endif; ?>
<table id="report-table"><thead><tr><th>#</th><th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead>
<tbody><?php $orders2 = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id WHERE DATE(o.order_date) BETWEEN '$from' AND '$to' ORDER BY o.order_date DESC");
$sn=1; while($o=$orders2->fetch_assoc()): ?>
<tr><td><?= $sn++ ?></td><td>#<?= $o['id'] ?></td><td><?= $o['user_name'] ?></td><td>₹<?= number_format($o['total_amount'],2) ?></td>
<td><?= $o['payment_method'] ?></td><td><?= ucfirst($o['status']) ?></td><td><?= date('d M Y',strtotime($o['order_date'])) ?></td></tr>
<?php endwhile; ?></tbody></table>
<script>
function exportCSV(){let csv='Order#,Customer,Total,Payment,Status,Date\n';
document.querySelectorAll('#report-table tbody tr').forEach(r=>{let d=[];
r.querySelectorAll('td').forEach(t=>d.push('"'+t.textContent.trim()+'"'));csv+=d.join(',')+'\n'});
let a=document.createElement('a');a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
a.download='sales_report_<?= $from ?>_to_<?= $to ?>.csv';a.click();}
</script>
<footer>&copy; 2025 Phone Phactory Admin Panel</footer></body></html>
