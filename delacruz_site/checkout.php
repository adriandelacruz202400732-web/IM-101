<?php
session_start();
include "database.php";
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: index.php");
    exit;
}

$total = 0;
$ids = implode(',', array_map('intval', array_keys($cart)));
$res = $conn->query("SELECT id, name, price FROM products WHERE id IN ($ids)");
while ($r = $res->fetch_assoc()) {
    $total += $r['price'] * ($cart[$r['id']] ?? 0);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-left"><h1>Checkout</h1></div>
    <div class="nav-right"><a href="cart.php" class="cart-link">← Back to Cart</a></div>
  </nav>

  <div class="container-center">
    <div class="form">
      <h3 style="margin-bottom:12px;">Customer Details</h3>

      <form action="process_order.php" method="post">
        <label>Full Name</label>
        <input type="text" name="fullname" required>

        <label>Email</label>
        <input type="email" name="email">

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Address</label>
        <textarea name="address" rows="3"></textarea>

        <button type="submit">Place Order • ₱<?= number_format($total,2) ?></button>
      </form>
    </div>
  </div>
</body>
</html>
