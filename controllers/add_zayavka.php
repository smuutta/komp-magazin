<?php
include("../config/db.php");

// Проверка, что все данные переданы и не пусты
if (
    empty($_POST['order_date']) ||
    empty($_POST['supplier_id']) ||
    empty($_POST['employee_id']) ||
    empty($_POST['product_id']) ||
    empty($_POST['quantity_ordered']) ||
    empty($_POST['expected_price'])
) {
    header("Location: ../views/form_zayavka.php?error=Заполните все поля!");
    exit();
}

$order_date = $_POST['order_date'];
$supplier_id = $_POST['supplier_id'];
$employee_id = $_POST['employee_id'];
$product_id = $_POST['product_id'];
$quantity_ordered = $_POST['quantity_ordered'];
$expected_price = $_POST['expected_price'];

// ——— SQL-инъекции: подготовленные выражения ———
$stmt1 = $conn->prepare("INSERT INTO purchase_order (supplier_id, employee_id, order_date) VALUES (?, ?, ?)");
$stmt1->bind_param("iis", $supplier_id, $employee_id, $order_date);

if ($stmt1->execute()) {
    $order_id = $conn->insert_id;

    $stmt2 = $conn->prepare("INSERT INTO order_line (order_id, product_id, quantity_ordered, expected_price) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("iiid", $order_id, $product_id, $quantity_ordered, $expected_price);

    if ($stmt2->execute()) {
        // Успех: редирект с сообщением
        header("Location: ../views/form_zayavka.php?success=1");
    } else {
        // Ошибка при добавлении строки заказа
        header("Location: ../views/form_zayavka.php?error=Ошибка при добавлении товара!");
    }
    $stmt2->close();
} else {
    // Ошибка при создании заявки
    header("Location: ../views/form_zayavka.php?error=Ошибка при создании заявки!");
}
$stmt1->close();

$conn->close();
exit();
?>