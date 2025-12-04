<?php
include "database.php";
$orders = $conn->query("SELECT o.*, c.fullname FROM orders o JOIN customers c ON c.customer_id=o.customer_id ORDER BY o.order_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>All Orders</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-left"><h1>Orders</h1></div>
    <div class="nav-right"><a href="index.php" class="cart-link">Back to Shop</a></div>
  </nav>

  <div class="container-center">
    <h2 style="margin:18px 0;">All Orders</h2>

    <table class="cart-table">
      <tr><th>Order</th><th>Customer</th><th>Total</th><th>Date</th><th></th></tr>
      <?php while ($o = $orders->fetch_assoc()): ?>
        <tr>
          <td>#<?= $o['order_id'] ?></td>
          <td><?= htmlspecialchars($o['fullname']) ?></td>
          <td>₱<?= number_format($o['total_amount'],2) ?></td>
          <td><?= $o['order_date'] ?></td>
          <td><a href="order_view.php?id=<?= $o['order_id'] ?>" class="btn">View</a></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
