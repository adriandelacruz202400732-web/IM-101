<?php
session_start();
include "database.php";


$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;


$products = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Shop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-left"><h1>My Shop</h1></div>
    <div class="nav-right"><a href="cart.php" class="cart-link">🛒 Cart (<?= $cart_count ?>)</a></div>
  </nav>

  <h2 class="products-title">Products</h2>

  <div class="products container-center">
    <?php while ($p = $products->fetch_assoc()): ?>
      <div class="product">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        
        <div class="price">₱<?= number_format($p['price'],2) ?></div>
        <div class="stock">Stock: <?= (int)$p['Quantity'] ?></div>

      
        <a class="btn" href="cart.php?action=add&id=<?= (int)$p['id'] ?>">Add to Cart</a>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>
