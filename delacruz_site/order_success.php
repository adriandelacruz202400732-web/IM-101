<?php
include "database.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = null;
if ($id) {
    $order = $conn->query("SELECT o.*, c.fullname FROM orders o JOIN customers c ON c.customer_id=o.customer_id WHERE order_id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order Success</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-left"><h1>Order Completed</h1></div>
    <div class="nav-right"><a href="index.php" class="cart-link">Back to Shop</a></div>
  </nav>

  <div class="container-center">
    <div style="background:#fff;padding:22px;border-radius:12px;margin-top:20px;box-shadow:0 4px 12px rgba(0,0,0,0.06);">
      <?php if ($order): ?>
        <h2>Thanks, <?= htmlspecialchars($order['fullname']) ?>!</h2>
        <p>Your order <strong>#<?= $order['order_id'] ?></strong> has been placed.</p>
        <p>Total: <strong>₱<?= number_format($order['total_amount'],2) ?></strong></p>
        <p><a href="order_view.php?id=<?= $order['order_id'] ?>" class="btn">View Order</a></p>
      <?php else: ?>
        <p>Order not found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
