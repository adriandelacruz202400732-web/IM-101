<?php
include "database.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = $conn->query("SELECT o.*, c.* FROM orders o JOIN customers c ON c.customer_id=o.customer_id WHERE o.order_id=$id")->fetch_assoc();
$items = $conn->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$id");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order #<?= $id ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-left"><h1>Order #<?= $id ?></h1></div>
    <div class="nav-right"><a href="orders.php" class="cart-link">← Back</a></div>
  </nav>

  <div class="container-center">
    <?php if (!$order): ?>
      <div style="background:#fff;padding:20px;border-radius:10px;">Order not found.</div>
    <?php else: ?>
      <div class="order-box">
        <h3>Customer: <?= htmlspecialchars($order['fullname']) ?></h3>
        <p><b>Phone:</b> <?= htmlspecialchars($order['phone']) ?> &nbsp; <b>Email:</b> <?= htmlspecialchars($order['email']) ?></p>
        <p><b>Address:</b> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
        <p><b>Order Date:</b> <?= $order['order_date'] ?></p>
        <p><b>Total:</b> ₱<?= number_format($order['total_amount'],2) ?></p>

        <h4 style="margin-top:12px;">Items</h4>
        <table class="cart-table" style="margin-top:8px">
          <tr><th>Product</th><th>Qty</th><th>Price</th><th>Line</th></tr>
          <?php while ($it = $items->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($it['name']) ?></td>
              <td><?= (int)$it['quantity'] ?></td>
              <td>₱<?= number_format($it['price'],2) ?></td>
              <td>₱<?= number_format($it['price'] * $it['quantity'],2) ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
