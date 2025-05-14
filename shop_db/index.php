<?php
$conn = new mysqli("localhost", "root", "", "shop_db");
$result = $conn->query("SELECT * FROM products");
$user_id = 1; // временно демо-пользователь
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 16px;
            transition: transform 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-add {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .card-img-top {
            height: 300px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center"><i class="bi bi-shop"></i> Каталог товаров</h1>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <!-- Добавляем изображение товара -->
                        <img src="images/<?php echo $row['image_url']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text fw-bold text-success"><?php echo $row['price']; ?> TMT</p>
                            <form action="add_to_cart.php" method="post" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <label for="quantity_<?php echo $row['id']; ?>" class="form-label mb-0">Количество:</label>
                                    <input type="number" name="quantity" id="quantity_<?php echo $row['id']; ?>" class="form-control form-control-sm w-50" value="1" min="1">
                                </div>
                                <button type="submit" class="btn btn-primary w-100 btn-add">
                                    <i class="bi bi-cart-plus"></i> Добавить в корзину
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center mt-5">
            <a href="cart.php" class="btn btn-outline-dark">
                <i class="bi bi-basket"></i> Перейти в корзину
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
