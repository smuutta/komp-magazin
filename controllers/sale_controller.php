<?php


class SaleController {
    private $conn;
    private $m;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->m    = new Sale($conn);
    }

    public function showForm() {
        // 1) Получим список сотрудников
        $emps = $this->conn
            ->query("SELECT employee_id AS id, full_name AS name FROM employee")
            ->fetch_all(MYSQLI_ASSOC);

        // 2) Список товаров с остатками
        $prodsRaw = $this->conn
            ->query("SELECT product_id AS id, name FROM product")
            ->fetch_all(MYSQLI_ASSOC);
        $prods = [];
        foreach ($prodsRaw as $p) {
            $p['stock'] = $this->m->getStock($p['id']);
            $prods[] = $p;
        }

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/sale/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    public function create() {
        $id = $this->m->create($_POST);
        if ($id === 0) {
            // недостаточно товара — показать ошибку
            $_GET['error'] = 'Недостаточно товара на складе';
            return $this->showForm();
        }
        header("Location: index.php?route=sale/success&id={$id}");
        exit;
    }

    public function success() {
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/sale/success.php';
        include __DIR__ . '/../partials/footer.php';
    }
}
