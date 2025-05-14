<?php
$conn = new mysqli("localhost", "root", "", "shop_db");

$product_id = $_POST['product_id'];
$user_id = $_POST['user_id'];
$quantity = $_POST['quantity'];

// Получаем имя товара и цену товара
$stmt_product = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result = $stmt_product->get_result();
$product = $result->fetch_assoc();
$product_name = $product['name'] ?? 'Неизвестно';
$product_price = $product['price'] ?? 0.00;  // Default price to 0 if not found

// Добавляем в корзину с именем товара и ценой
$stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity, product_name, price) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiisd", $user_id, $product_id, $quantity, $product_name, $product_price);
$stmt->execute();

header("Location: cart.php");
exit();
?>
