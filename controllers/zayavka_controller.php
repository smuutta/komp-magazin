<?php


require_once __DIR__ . '/../model.php';

class ZayavkaController
{
    private \mysqli $conn;
    private Zayavka $m;

    // === 1) Конструктор ===
    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
        $this->m    = new Zayavka($conn);
    }

    // === 2) Список заявок + форма создания ===
    public function form()
    {
        // Справочники
        $emps = $this->conn
            ->query("SELECT employee_id AS id, full_name AS name FROM employee")
            ->fetch_all(MYSQLI_ASSOC);

        $sups = $this->conn
            ->query("SELECT supplier_id AS id, name FROM supplier")
            ->fetch_all(MYSQLI_ASSOC);

        // Продукты + уже заказано
        $prodsRaw = $this->conn
            ->query("SELECT product_id AS id, name FROM product")
            ->fetch_all(MYSQLI_ASSOC);

        $prods = [];
        foreach ($prodsRaw as $p) {
            $p['req'] = $this->m->getRequested($p['id']);
            $prods[]  = $p;
        }

        // Количество строк в форме
        $count = isset($_GET['count'])
               ? max(1, (int)$_GET['count'])
               : 1;

        // Существующие заявки
        $orders = $this->m->getAll();

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/zayavka/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === 3) Создать новую заявку ===
    public function create()
    {
        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'employee_id' => (int)$_POST['employee_id'],
            'order_date'  => $_POST['order_date'],
            'lines'       => []
        ];

        foreach ($_POST['lines'] ?? [] as $ln) {
            $pid   = (int)($ln['product_id'] ?? 0);
            $qty   = (int)($ln['qty']        ?? 0);
            $price = (float)($ln['price']    ?? 0);

            if ($pid > 0 && $qty > 0) {
                $data['lines'][] = [
                    'product_id' => $pid,
                    'qty'        => $qty,
                    'price'      => $price
                ];
            }
        }

        $this->m->create($data);

        header("Location: index.php?route=zayavka/form&success=created");
        exit;
    }

    // === 4) Просмотр одной заявки ===
    public function view()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?route=zayavka/form");
            exit;
        }

        $order = $this->m->getById($id);

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/zayavka/view.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === 5) Показать форму редактирования ===
    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?route=zayavka/form");
            exit;
        }

        // Заголовок + строки
        $order = $this->m->getById($id);

        // Справочники
        $emps = $this->conn
            ->query("SELECT employee_id AS id, full_name AS name FROM employee")
            ->fetch_all(MYSQLI_ASSOC);

        $sups = $this->conn
            ->query("SELECT supplier_id AS id, name FROM supplier")
            ->fetch_all(MYSQLI_ASSOC);

        // Продукты + уже заказано
        $prodsRaw = $this->conn
            ->query("SELECT product_id AS id, name FROM product")
            ->fetch_all(MYSQLI_ASSOC);

        $prods = [];
        foreach ($prodsRaw as $p) {
            $p['req'] = $this->m->getRequested($p['id']);
            $prods[]  = $p;
        }

        // Количество строк из заявки
        $count  = count($order['lines']);
        $orders = $this->m->getAll(); // для списка внизу

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/zayavka/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === 6) Обновить заявку ===
    public function update()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?route=zayavka/form&error=invalid");
            exit;
        }

        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'employee_id' => (int)$_POST['employee_id'],
            'order_date'  => $_POST['order_date'],
            'lines'       => []
        ];

        foreach ($_POST['lines'] ?? [] as $ln) {
            $pid   = (int)($ln['product_id'] ?? 0);
            $qty   = (int)($ln['qty']        ?? 0);
            $price = (float)($ln['price']    ?? 0);

            if ($pid > 0 && $qty > 0) {
                $data['lines'][] = [
                    'product_id' => $pid,
                    'qty'        => $qty,
                    'price'      => $price
                ];
            }
        }

        $this->m->update($id, $data);

        header("Location: index.php?route=zayavka/form&success=updated");
        exit;
    }

    // === 7) Удалить заявку ===
    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->m->delete($id);
        }

        header("Location: index.php?route=zayavka/form");
        exit;
    }

    // === 8) Страница успеха ===
    public function success()
    {
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/zayavka/success.php';
        include __DIR__ . '/../partials/footer.php';
    }
}
