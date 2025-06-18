<?php
include("../config/db.php");

// Проверка метода
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/form_sale.php");
    exit();
}

// Получение и простая валидация данных
$employee_id    = isset($_POST['employee_id']) ? (int) $_POST['employee_id'] : 0;
$product_id     = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity_sold  = isset($_POST['quantity_sold']) ? (int) $_POST['quantity_sold'] : 0;
$sale_price     = isset($_POST['sale_price']) ? (float) $_POST['sale_price'] : 0.0;

// Проверка: все поля должны быть заполнены
if ($employee_id === 0  $product_id === 0  $quantity_sold <= 0 || $sale_price <= 0) {
    header("Location: ../views/form_sale.php?error=missing_fields");
    exit();
}

// Проверка остатка на складе
$stock_query = $conn->prepare("
    SELECT 
        COALESCE((
            SELECT SUM(quantity_received) FROM invoice_line WHERE product_id = ?
        ), 0) - 
        COALESCE((
            SELECT SUM(quantity_sold) FROM sale_line WHERE product_id = ?
        ), 0) AS stock
");
$stock_query->bind_param("ii", $product_id, $product_id);
$stock_query->execute();
$result = $stock_query->get_result();
$row = $result->fetch_assoc();
$current_stock = isset($row['stock']) ? (int)$row['stock'] : 0;

if ($quantity_sold > $current_stock) {
    header("Location: ../views/form_sale.php?error=not_enough_stock");
    exit();
}

// Проверка существования сотрудника
$check_emp = $conn->prepare("SELECT 1 FROM employee WHERE employee_id = ?");
$check_emp->bind_param("i", $employee_id);
$check_emp->execute();
$res = $check_emp->get_result();
if ($res->num_rows === 0) {
    header("Location: ../views/form_sale.php?error=invalid_employee");
    exit();
}

// Создаём продажу
$insert_sale = $conn->prepare("INSERT INTO sale (employee_id) VALUES (?)");
$insert_sale->bind_param("i", $employee_id);
if (!$insert_sale->execute()) {
    header("Location: ../views/form_sale.php?error=unknown");
    exit();
}
$sale_id = $conn->insert_id;

// Добавляем товар к продаже
$insert_item = $conn->prepare("
    INSERT INTO sale_line (sale_id, product_id, quantity_sold, sale_price)
    VALUES (?, ?, ?, ?)
");
$insert_item->bind_param("iiid", $sale_id, $product_id, $quantity_sold, $sale_price);
if (!$insert_item->execute()) {
    header("Location: ../views/form_sale.php?error=unknown");
    exit();
}

// Успешно
header("Location: ../views/form_sale.php?success=1");
exit();
?>