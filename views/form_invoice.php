<?php include("../config/db.php"); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание приходной накладной</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { width: 420px; margin: 40px auto; background: #fff; padding: 22px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        label { display: block; margin-top: 14px; }
        select, input[type="number"], input[type="date"] {
            width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="submit"] {
            margin-top: 18px; width: 100%; padding: 10px;
            background: #2979ff; color: #fff; border: none; border-radius: 4px; font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover { background: #1565c0; }
        .back-link { display: block; margin-top: 16px; text-align: center; }
        .error { color: #d32f2f; text-align: center; margin-top: 10px; }
        .success { color: #388e3c; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Создание приходной накладной</h2>

    <?php
    if (isset($_GET['success'])) {
        echo '<div class="success">Накладная успешно добавлена!</div>';
    } elseif (isset($_GET['error'])) {
        echo '<div class="error">'.htmlspecialchars($_GET['error']).'</div>';
    }
    ?>

    <form action="../controllers/add_invoice.php" method="post">
        <label>Дата накладной:</label>
        <input type="date" name="invoice_date" required value="<?php echo date('Y-m-d'); ?>">

        <label>Сотрудник:</label>
        <select name="employee_id" required>
            <option value="">Выберите сотрудника</option>
            <?php
            $res = $conn->query("SELECT employee_id, full_name FROM employee");
            while($row = $res->fetch_assoc()) {
                echo "<option value='{$row['employee_id']}'>{$row['full_name']}</option>";
            }
            ?>
        </select>

        <label>Поставщик:</label>
        <select name="supplier_id" required>
            <option value="">Выберите поставщика</option>
            <?php
            $res = $conn->query("SELECT supplier_id, name FROM supplier");
            while($row = $res->fetch_assoc()) {
                echo "<option value='{$row['supplier_id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <label>Заявка:</label>
        <select name="order_id" required>
            <option value="">Выберите заявку</option>
            <?php
            $res = $conn->query("SELECT order_id, order_date FROM purchase_order");
            while($row = $res->fetch_assoc()) {
                echo "<option value='{$row['order_id']}'>Заявка #{$row['order_id']} от {$row['order_date']}</option>";
            }
            ?>
        </select>

        <label>Товар:</label>
        <select name="product_id" required>
            <option value="">Выберите товар</option>
            <?php
            $res = $conn->query("SELECT product_id, name FROM product");
            while($row = $res->fetch_assoc()) {
                echo "<option value='{$row['product_id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <label>Количество получено:</label>
        <input type="number" name="quantity_received" min="1" required>

        <label>Фактическая цена (₽):</label>
        <input type="number" step="0.01" name="actual_price" min="0.01" required>

        <input type="submit" value="Создать накладную">
    </form>
    <a class="back-link" href="../index.php">← На главную</a>
</div>
</body>
</html>