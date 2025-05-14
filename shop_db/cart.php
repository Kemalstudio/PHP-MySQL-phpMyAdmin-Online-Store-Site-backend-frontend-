<?php
// Предполагается, что соединение $conn уже установлено, как в вашем коде
$conn = new mysqli("localhost", "root", "", "shop_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Лучше добавить проверку соединения
}
$conn->set_charset("utf8mb4"); // Рекомендуется для корректной работы с кириллицей

$user_id = 1; // Пример ID пользователя

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_cart'])) {
    // Очистить все товары для пользователя
    $clear_sql = "DELETE FROM cart_items WHERE user_id = ?";
    $clear_stmt = $conn->prepare($clear_sql);
    if ($clear_stmt) {
        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();
        $clear_stmt->close();
    }
    header("Location: cart.php"); // Перезагрузить страницу после очистки
    exit();
}

// Обновленный SQL-запрос для получения цены товара и расчета суммы
$sql = "SELECT p.name, p.price, c.quantity, c.added_at 
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Обработка ошибки подготовки запроса
    die("Error preparing statement: " . $conn->error);
}

$cart_items = [];
$total_cart_price = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total_cart_price += $row['subtotal'];
        $cart_items[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5; /* Немного другой фон для свежести */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Более современный шрифт */
        }
        .cart-container {
            max-width: 900px; /* Немного шире для доп. колонок */
            margin: 40px auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 20px; /* Более скругленные углы */
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); /* Более выраженная тень */
        }
        .cart-title {
            font-size: 2.2rem; /* Чуть больше */
            font-weight: 700;
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #333;
        }
        .table th {
            font-weight: 600; /* Полужирный для заголовков */
        }
        .table td {
            vertical-align: middle;
        }
        .product-name {
            font-weight: 500;
        }
        .price-highlight {
            font-weight: 600;
            color: #007bff; /* Синий акцент на итоговой цене */
        }
        .footer-actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
         /* Стиль для иконок в таблице */
        .table .bi {
            font-size: 1.1rem; /* Немного увеличить иконки */
        }
        .table thead.table-dark th { /* Для лучшей читаемости */
            background-color: #2c3e50; /* Более мягкий темный */
            border-color: #34495e;
        }
        .total-row th, .total-row td { /* Стиль для строки "Итого" */
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <div class="cart-title">
        <i class="bi bi-cart-check-fill text-primary" style="font-size: 2.5rem;"></i> Ваша корзина
    </div>

    <?php if (count($cart_items) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 40%;">Товар</th>
                        <th scope="col" class="text-end">Цена за ед.</th>
                        <th scope="col" class="text-center">Количество</th>
                        <th scope="col" class="text-end">Сумма</th>
                        <th scope="col" class="text-center">Добавлено</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box-seam text-muted me-3" style="font-size: 1.5rem;"></i> 
                                    <span class="product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td class="text-end"><?php echo number_format($item['price'], 2, ',', ' '); ?> TMT</td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="text-end fw-semibold"><?php echo number_format($item['subtotal'], 2, ',', ' '); ?> TMT</td>
                            <td class="text-center">
                                <i class="bi bi-calendar-plus text-muted me-1"></i>
                                <?php echo date("d.m.Y H:i", strtotime($item['added_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light total-row">
                        <th colspan="3" class="text-end align-center">Общая стоимость:</th>
                        <td class="text-end align-center price-highlight"><?php echo number_format($total_cart_price, 2, ',', ' '); ?> TMT</td>
                        <td class="text-center"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer-actions">
            <a href="index.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left-circle me-2"></i> Продолжить покупки
            </a>
            <form method="post" class="m-0">
                <button type="submit" name="clear_cart" class="btn btn-danger btn-lg">
                    <i class="bi bi-trash3-fill me-2"></i> Очистить корзину
                </button>
            </form>
        </div>

    <?php else: ?>
        <div class="alert alert-info text-center p-4" role="alert">
            <i class="bi bi-emoji-frown fs-1 d-block mb-2"></i>
            <h4 class="alert-heading">Ваша корзина пуста!</h4>
            <p>Похоже, вы еще ничего не добавили в корзину. Перейдите в каталог, чтобы выбрать товары.</p>
            <hr>
            <a href="index.php" class="btn btn-primary btn-lg">
                <i class="bi bi-shop me-2"></i> Перейти к товарам
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (необязательно для отображения, но полезно для некоторых компонентов) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if ($stmt) {
    $stmt->close();
}
$conn->close();
?>