<?php
session_start();
include "database.php";


if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];


function intv($v){ return intval($v); }


$action = $_GET['action'] ?? null;
if ($action === 'add' && isset($_GET['id'])) {
    $id = intv($_GET['id']);
    
    $r = $conn->query("SELECT Quantity FROM products WHERE id=$id")->fetch_assoc();
    if ($r && $r['Quantity'] > 0) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'remove' && isset($_GET['id'])) {
    $id = intv($_GET['id']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    
    foreach ($_POST['qty'] as $id => $q) {
        $id = intv($id); $q = max(0,intv($q));
        if ($q <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            
            $stock = $conn->query("SELECT Quantity FROM products WHERE id=$id")->fetch_assoc()['Quantity'] ?? 0;
            $_SESSION['cart'][$id] = min($q, $stock);
        }
    }
    header("Location: cart.php");
    exit;
}


$cart = $_SESSION['cart'];
$items = [];
$subtotal = 0.0;
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($r = $res->fetch_assoc()) {
        $r['qty'] = $cart[$r['id']] ?? 0;
        $r['line'] = $r['price'] * $r['qty'];
        $subtotal += $r['line'];
        $items[] = $r;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cart</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-left"><h1>My Shop</h1></div>
    <div class="nav-right"><a href="index.php" class="cart-link">← Continue shopping</a></div>
  </nav>

  <div class="container-center">
    <h2 style="margin:18px 0;">Your Cart</h2>

    <?php if (empty($items)): ?>
        <div style="background:#fff;padding:20px;border-radius:10px;">Your cart is empty.</div>
    <?php else: ?>
        <form method="post">
        <table class="cart-table">
            <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Line</th><th></th></tr>
            <?php foreach($items as $it): ?>
                <tr>
                    <td><?= htmlspecialchars($it['name']) ?></td>
                    <td>₱<?= number_format($it['price'],2) ?></td>
                    <td>
                        <input class="qty-input" type="number" name="qty[<?= (int)$it['id'] ?>]" value="<?= (int)$it['qty'] ?>" min="0" max="<?= (int)$it['Quantity'] ?>">
                    </td>
                    <td>₱<?= number_format($it['line'],2) ?></td>
                    <td><a href="cart.php?action=remove&id=<?= (int)$it['id'] ?>" style="color:#c0392b;text-decoration:none;">Remove</a></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align:right;padding:12px;font-weight:700">Total:</td>
                <td style="font-weight:700">₱<?= number_format($subtotal,2) ?></td>
                <td></td>
            </tr>
        </table>

        <div style="display:flex;gap:12px;margin-top:14px;">
            <button type="submit" name="update_cart" class="btn">Update Cart</button>
            <a href="checkout.php" class="btn" style="background:#28a745">Proceed to Checkout</a>
        </div>
        </form>
    <?php endif; ?>
  </div>
</body>
</html>
