<?php
include("../config/db.php");

// Проверка, что все нужные поля пришли и не пусты
if (
    empty($_POST['invoice_date']) ||
    empty($_POST['order_id']) ||
    empty($_POST['product_id']) ||
    empty($_POST['quantity_received']) ||
    empty($_POST['actual_price']) ||
    empty($_POST['employee_id']) ||
    empty($_POST['supplier_id'])
) {
    header("Location: ../views/form_invoice.php?error=Заполните все поля!");
    exit();
}

$invoice_date = $_POST['invoice_date'];
$order_id = $_POST['order_id'];
$product_id = $_POST['product_id'];
$quantity_received = $_POST['quantity_received'];
$actual_price = $_POST['actual_price'];
$employee_id = $_POST['employee_id'];
$supplier_id = $_POST['supplier_id'];

// Безопасный INSERT для накладной
$stmt1 = $conn->prepare(
    "INSERT INTO invoice (order_id, supplier_id, employee_id, invoice_date) VALUES (?, ?, ?, ?)"
);
$stmt1->bind_param("iiis", $order_id, $supplier_id, $employee_id, $invoice_date);

if ($stmt1->execute()) {
    $invoice_id = $conn->insert_id;

    // Безопасный INSERT для строки накладной
    $stmt2 = $conn->prepare(
        "INSERT INTO invoice_line (invoice_id, product_id, quantity_received, actual_price) VALUES (?, ?, ?, ?)"
    );
    $stmt2->bind_param("iiid", $invoice_id, $product_id, $quantity_received, $actual_price);

    if ($stmt2->execute()) {
        header("Location: ../views/form_invoice.php?success=1");
    } else {
        header("Location: ../views/form_invoice.php?error=Ошибка при добавлении товара!");
    }
    $stmt2->close();
} else {
    header("Location: ../views/form_invoice.php?error=Ошибка при создании накладной!");
}
$stmt1->close();
$conn->close();
exit();
?>