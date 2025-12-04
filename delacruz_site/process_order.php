<?php
session_start();
include "database.php";

$cart = $_SESSION['cart'] ?? [];
if (empty($cart) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

if (trim($fullname) === '') {
    die("Fullname is required.");
}

$conn->begin_transaction();

try {
  
    $stmt = $conn->prepare("INSERT INTO customers (fullname, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $phone, $address);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();

    
    $total = 0.0;
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $conn->query("SELECT id, price, Quantity FROM products WHERE id IN ($ids) FOR UPDATE"); // lock rows

    $productData = [];
    while ($r = $res->fetch_assoc()) {
        $productData[$r['id']] = $r;
    }

    foreach ($cart as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if (!isset($productData[$pid])) throw new Exception("Product not found: $pid");
        if ($productData[$pid]['Quantity'] < $qty) throw new Exception("Insufficient stock for product ID $pid");
        $total += $productData[$pid]['price'] * $qty;
    }

  
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $customer_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmtUpdate = $conn->prepare("UPDATE products SET Quantity = Quantity - ? WHERE id = ?");
    foreach ($cart as $pid => $qty) {
        $pid = (int)$pid; $qty = (int)$qty;
        $price = $productData[$pid]['price'];
        $stmtItem->bind_param("iiid", $order_id, $pid, $qty, $price);
        $stmtItem->execute();

        $stmtUpdate->bind_param("ii", $qty, $pid);
        $stmtUpdate->execute();
    }
    $stmtItem->close();
    $stmtUpdate->close();

    $conn->commit();

    unset($_SESSION['cart']);

    header("Location: order_success.php?id=" . $order_id);
    exit;
} catch (Exception $e) {
    $conn->rollback();
   
    die("Order failed: " . htmlspecialchars($e->getMessage()));
}
