<?php

class ReportController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        // собираем строки продаж
        $sql = "
          SELECT 
            sl.sale_id    AS sale_id,
            s.sale_date   AS sale_date,
            e.full_name   AS employee,
            p.name        AS product,
            sl.quantity_sold AS qty,
            sl.sale_price    AS price,
            (sl.quantity_sold * sl.sale_price) AS total_line
          FROM sale_line sl
          JOIN sale  s ON sl.sale_id = s.sale_id
          JOIN employee e ON s.employee_id = e.employee_id
          JOIN product   p ON sl.product_id = p.product_id
          -- сортируем только по дате, от новых к старым
          ORDER BY s.sale_date DESC
        ";
        $res = $this->conn->query($sql);
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        // общий итог
        $grandTotal = array_reduce($rows, function($sum,$r){
            return $sum + $r['total_line'];
        }, 0.0);

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/report.php';
        include __DIR__ . '/../partials/footer.php';
    }
}