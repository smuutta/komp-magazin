<?php

require_once __DIR__ . '/../model.php';

class InvoiceController
{
    private \mysqli $conn;
    private Invoice  $invoiceM;
    private Zayavka  $orderM;

    // === Конструктор ===
    public function __construct(\mysqli $conn)
    {
        $this->conn     = $conn;
        $this->invoiceM = new Invoice($conn);
        $this->orderM   = new Zayavka($conn);
    }

    // === Показать форму создания накладной ===
    public function showForm()
    {
        // Справочники
        $emps = $this->conn
            ->query("SELECT employee_id AS id, full_name AS name FROM employee")
            ->fetch_all(MYSQLI_ASSOC);
        $sups = $this->conn
            ->query("SELECT supplier_id AS id, name FROM supplier")
            ->fetch_all(MYSQLI_ASSOC);

        // Список ожидающих заявок
        $orders = $this->orderM->getPending();

        // Если выбрали конкретную заявку — загрузим её данные
        $orderId = isset($_GET['order_id'])
                 ? (int)$_GET['order_id']
                 : 0;
        $order   = null;
        $lines   = [];
        if ($orderId) {
            // Заголовок заявки
            $order = $this->conn
                ->query("
                    SELECT supplier_id, employee_id, order_date
                      FROM purchase_order
                     WHERE order_id = {$orderId}
                ")
                ->fetch_assoc();

            // Строки заявки
            $lines = $this->orderM->getLines($orderId);
            $count = count($lines);

        } else {
            // Ручной ввод: число строк через GET[count]
            $count = isset($_GET['count'])
                   ? max(1, (int)$_GET['count'])
                   : 1;
        }

        // Товары + остатки
        $prodsRaw = $this->conn
            ->query("SELECT product_id AS id, name FROM product")
            ->fetch_all(MYSQLI_ASSOC);

        $prods = [];
        foreach ($prodsRaw as $p) {
            $p['stock'] = $this->invoiceM->getStock($p['id']);
            $prods[]    = $p;
        }

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/invoice/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === Создать накладную ===
    public function create()
    {
        // Приём по заявке?
        $orderId = isset($_POST['order_id'])
                 ? (int)$_POST['order_id']
                 : 0;

        if ($orderId) {
            // Берём данные из заявки
            $row = $this->conn
                ->query("
                    SELECT supplier_id, employee_id, order_date
                      FROM purchase_order
                     WHERE order_id = {$orderId}
                ")
                ->fetch_assoc();

            $lines = $this->orderM->getLines($orderId);

            $data = [
                'supplier_id'  => $row['supplier_id'],
                'employee_id'  => $row['employee_id'],
                'invoice_date' => $row['order_date'],
                'lines'        => array_map(
                    fn($ln) => [
                        'product_id' => $ln['product_id'],
                        'qty'        => $ln['qty'],
                    ],
                    $lines
                )
            ];

            // Создаём накладную и переводим заявку в статус received
            $invId = $this->invoiceM->create($data);
            $this->orderM->updateStatus($orderId, 'received');

            header("Location: index.php?route=invoice/success&id={$invId}");
            exit;
        }

        // --- Ручной ввод без заявки ---
        $data = [
            'supplier_id'  => (int)($_POST['supplier_id']  ?? 0),
            'employee_id'  => (int)($_POST['employee_id']  ?? 0),
            'invoice_date' => $_POST['invoice_date']       ?? '',
            'lines'        => []
        ];

        if (!empty($_POST['lines']) && is_array($_POST['lines'])) {
            foreach ($_POST['lines'] as $ln) {
                $pid = (int)($ln['product_id'] ?? 0);
                $qty = (int)($ln['qty']        ?? 0);
                if ($pid > 0 && $qty > 0) {
                    $data['lines'][] = [
                        'product_id' => $pid,
                        'qty'        => $qty
                    ];
                }
            }
        }

        $invId = $this->invoiceM->create($data);

        header("Location: index.php?route=invoice/success&id={$invId}");
        exit;
    }

    // === Успешное создание ===
    public function success()
    {
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/invoice/success.php';
        include __DIR__ . '/../partials/footer.php';
    }
}
