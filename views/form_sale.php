<?php
include("../config/db.php");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Продажа товара</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { width: 400px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        label { display: block; margin-top: 14px; }
        select, input[type="number"] {
            width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            margin-top: 18px; width: 100%; padding: 10px;
            background: #388e3c; color: #fff; border: none; border-radius: 4px; font-size: 16px;
            cursor: pointer;
        }
        button:hover { background: #2e7d32; }
        .back-link { display: block; margin-top: 16px; text-align: center; }
        .error { color: #d32f2f; text-align: center; margin-top: 10px; }
        .success { color: #388e3c; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">

<h2>Продажа товара (чек)</h2>

<?php
// Сообщения об ошибке или успехе
if (isset($_GET['success'])) {
    echo "<div class='success'>✅ Чек успешно пробит!</div>";
}
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'not_enough_stock':
            echo "<div class='error'>❌ Недостаточно товара на складе.</div>";
            break;
        case 'invalid_employee':
            echo "<div class='error'>❌ Указан несуществующий сотрудник.</div>";
            break;
        default:
            echo "<div class='error'>❌ Неизвестная ошибка.</div>";
    }
}
?>

<form action="../controllers/add_sale.php" method="post">

    <!-- Сотрудник-кассир -->
    <label>Сотрудник (кассир):</label>
    <select name="employee_id" required>
        <option value="">— выберите сотрудника —</option>
        <?php
        $emp = $conn->query("SELECT employee_id, full_name FROM employee ORDER BY full_name");
        while ($row = $emp->fetch_assoc()) {
            $id   = $row['employee_id'];
            $name = htmlspecialchars($row['full_name']);
            echo "<option value='$id'>$name</option>";
        }
        ?>
    </select>

    <!-- Товар с положительным остатком -->
    <label>Товар (только в наличии):</label>
    <select name="product_id" required>
        <option value="">— выберите товар —</option>
        <?php
        $query = "
            SELECT 
                p.product_id,
                p.name,
                COALESCE(ins.total_in, 0) - COALESCE(outs.total_out, 0) AS stock
            FROM product p
            LEFT JOIN (
                SELECT product_id, SUM(quantity_received) AS total_in
                FROM invoice_line
                GROUP BY product_id
            ) ins  USING (product_id)
            LEFT JOIN (
                SELECT product_id, SUM(quantity_sold) AS total_out
                FROM sale_line
                GROUP BY product_id
            ) outs USING (product_id)
            HAVING stock > 0
            ORDER BY p.name
        ";
        $res = $conn->query($query);
        while ($row = $res->fetch_assoc()) {
            $id    = $row['product_id'];
            $name  = htmlspecialchars($row['name']);
            $stock = (int)$row['stock'];
            echo "<option value='$id'>$name (в наличии: $stock)</option>";
        }
        ?>
    </select>

    <label>Количество:</label>
    <input type="number" name="quantity_sold" min="1" required>

    <label>Цена продажи (₽):</label>
    <input type="number" step="0.01" name="sale_price" min="0.01" required>

    <button type="submit">Пробить чек</button>
</form>
<a class="back-link" href="../index.php">← На главную</a>
</div>
</body>
</html>