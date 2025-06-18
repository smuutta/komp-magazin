<?php
include("../config/db.php");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчёт по продажам</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { width: 600px; margin: 40px auto; background: #fff; padding: 26px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px;}
        th, td { padding: 8px 10px; border: 1px solid #ccc; text-align: center;}
        th { background: #e3eaff; }
        tfoot td { font-weight: bold; background: #f0f0f0; }
        .back-link { display: block; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
<h2>Отчёт по продажам</h2>

<?php
$res = $conn->query("
    SELECT 
        p.name AS product_name,
        SUM(sl.quantity_sold) AS total_quantity,
        SUM(sl.quantity_sold * sl.sale_price) AS total_revenue
    FROM sale_line sl
    JOIN product p ON sl.product_id = p.product_id
    GROUP BY sl.product_id
");

$total_sum = 0;

echo "<table>";
echo "<tr><th>Товар</th><th>Продано (шт)</th><th>Выручка (₽)</th></tr>";

while($row = $res->fetch_assoc()) {
    $total_sum += $row['total_revenue'];
    echo "<tr>";
    echo "<td>{$row['product_name']}</td>";
    echo "<td>{$row['total_quantity']}</td>";
    echo "<td>".number_format($row['total_revenue'], 2, ',', ' ')."</td>";
    echo "</tr>";
}

echo "<tfoot><tr>
        <td colspan='2'>Итого выручка:</td>
        <td>".number_format($total_sum, 2, ',', ' ')."</td>
      </tr></tfoot>";
echo "</table>";
?>

<a class="back-link" href="../index.php">← На главную</a>
</div>
</body>
</html>