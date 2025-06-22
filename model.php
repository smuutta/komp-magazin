<?php

class Invoice {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // === Текущий остаток товара на складе
    public function getStock(int $productId): int {
        // Пришло по накладным
        $rec = $this->conn
        ->query("SELECT COALESCE(SUM(quantity_received),0) AS rec 
        FROM invoice_line 
    WHERE product_id = {$productId}")
        ->fetch_assoc()['rec'];
        // Ушло по продажам
        $sold = $this->conn
        ->query("SELECT COALESCE(SUM(quantity_sold),0) AS sold 
        FROM sale_line 
    WHERE product_id = {$productId}")
        ->fetch_assoc()['sold'];
        return $rec - $sold;
    }

    // === Создать накладную вместе со строками
    public function create(array $d): int {
        // 1) Создаём заголовок накладной
        $stmt = $this->conn->prepare(
        "INSERT INTO invoice (supplier_id, employee_id, invoice_date) 
        VALUES (?, ?, ?)"
        );
        $stmt->bind_param('iis',
        $d['supplier_id'],
        $d['employee_id'],
        $d['invoice_date']
        );
        $stmt->execute();
        $invoiceId = $stmt->insert_id;
        $stmt->close();

        // 2) Обрабатываем массив строк прихода
        $stmtLine = $this->conn->prepare(
        "INSERT INTO invoice_line (invoice_id, product_id, quantity_received) 
        VALUES (?, ?, ?)"
        );
        foreach ($d['lines'] as $line) {
            $pid = (int)$line['product_id'];
            $qty = (int)$line['qty'];
            // можно дополнительно проверять, что qty>0
            $stmtLine->bind_param('iii', $invoiceId, $pid, $qty);
            $stmtLine->execute();
        }
        $stmtLine->close();

        return $invoiceId;
    }

    // === Получить все накладные (без линий)
    public function getAll(): array {
        $res = $this->conn->query(
        "SELECT invoice_id AS id, supplier_id, employee_id, invoice_date, status 
        FROM invoice"
        );
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}

class Sale {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // === Текущий остаток товара на складе
    public function getStock(int $productId): int {
        // Сумма пришедшего
        $r = $this->conn->query(
        "SELECT COALESCE(SUM(quantity_received),0) AS rec 
        FROM invoice_line 
    WHERE product_id = {$productId}"
        )->fetch_assoc()['rec'];
        // Сумма проданного
        $s = $this->conn->query(
        "SELECT COALESCE(SUM(quantity_sold),0) AS sold 
        FROM sale_line 
    WHERE product_id = {$productId}"
        )->fetch_assoc()['sold'];
        return $r - $s;
    }

    // === Создать продажу и строку продажи, вернуть ID чека, если недостаточно товара — вернуть 0
    public function create(array $d): int {
        $prod = (int)$d['product_id'];
        $qty  = (int)$d['quantity'];
        $price= (float)$d['price'];

        // 1) Проверяем остаток
        if ($this->getStock($prod) < $qty) {
            return 0;   // сигнализируем контроллеру об ошибке
        }

        // 2) Вставляем header-чек
        $stmt = $this->conn->prepare(
        "INSERT INTO sale (employee_id, sale_date) VALUES (?, ?)"
        );
        $stmt->bind_param('is', $d['employee_id'], $d['sale_date']);
        $stmt->execute();
        $saleId = $stmt->insert_id;
        $stmt->close();

        // 3) Вставляем строку продажи
        $stmt = $this->conn->prepare(
        "INSERT INTO sale_line (sale_id, product_id, quantity_sold, sale_price)
        VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('iiid', $saleId, $prod, $qty, $price);
        $stmt->execute();
        $stmt->close();

        return $saleId;
    }
}
class Zayavka {
    private \mysqli $conn;

    public function __construct(\mysqli $conn) {
        $this->conn = $conn;
    }

    // === Создать новую заявку (header + строки) и вернуть её ID
    public function create(array $d): int {
        // 1) Вставляем заголовок
        $stmt = $this->conn->prepare(
        "INSERT INTO purchase_order (supplier_id, employee_id, order_date)
        VALUES (?, ?, ?)"
        );
        $stmt->bind_param('iis', 
        $d['supplier_id'],
        $d['employee_id'],
        $d['order_date']
        );
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();

        // 2) Вставляем строки
        $stmt = $this->conn->prepare(
        "INSERT INTO order_line (order_id, product_id, quantity_ordered, expected_price)
        VALUES (?, ?, ?, ?)"
        );
        foreach ($d['lines'] as $ln) {
            $stmt->bind_param('iiid',
            $orderId,
            $ln['product_id'],
            $ln['qty'],
            $ln['price']
            );
            $stmt->execute();
        }
        $stmt->close();

        return $orderId;
    }

    // === Вернуть список всех заявок (заголовки)
    public function getAll(): array {
        $res = $this->conn->query(
        "SELECT 
        order_id   AS id,
        supplier_id,
        employee_id,
        order_date,
        status
        FROM purchase_order
        ORDER BY order_date DESC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getPending(): array {
        $stmt = $this->conn->prepare(
        "SELECT
        order_id   AS id,
        supplier_id,
        employee_id,
        order_date,
        status
        FROM purchase_order
        WHERE status = 'created'
        ORDER BY order_date DESC"
        );
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    // === Вернуть строки конкретной заявки
    public function getLines(int $orderId): array {
        $stmt = $this->conn->prepare(
        "SELECT
        product_id,
        quantity_ordered AS qty,
        expected_price   AS price
        FROM order_line
        WHERE order_id = ?"
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $lines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $lines;
    }
    public function updateStatus(int $orderId, string $newStatus): bool {
        $stmt = $this->conn->prepare(
        "UPDATE purchase_order
        SET status = ?
        WHERE order_id = ?"
        );
        $stmt->bind_param('si', $newStatus, $orderId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // === сколько уже заказано данного товара
    public function getRequested(int $productId): int {
        $stmt = $this->conn->prepare(
        "SELECT COALESCE(SUM(quantity_ordered),0) AS req
        FROM order_line
        WHERE product_id = ?"
        );
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();
        return (int)($row['req'] ?? 0);
    }

    // === Получить заголовок и строки одной заявки
    public function getById(int $orderId): array {
        // 1) Заголовок
        $stmt = $this->conn->prepare(
        "SELECT 
        order_id   AS id,
        supplier_id,
        employee_id,
        order_date,
        status
        FROM purchase_order
        WHERE order_id = ?"
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $header = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();

        // 2) Строки заявки
        $stmt = $this->conn->prepare(
        "SELECT 
        product_id,
        quantity_ordered AS qty,
        expected_price   AS price
        FROM order_line
        WHERE order_id = ?"
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $lines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        //  строки к заголовку
        $header['lines'] = $lines;
        return $header;
    }

    // === Обновить заявку: перезаписать заголовок и все строки
    public function update(int $orderId, array $d): bool {
        $this->conn->begin_transaction();
        try {
            // обновляем заголовок
            $stmt = $this->conn->prepare(
            "UPDATE purchase_order
            SET supplier_id = ?, employee_id = ?, order_date = ?
            WHERE order_id = ?"
            );
            $stmt->bind_param('iisi',
            $d['supplier_id'],
            $d['employee_id'],
            $d['order_date'],
            $orderId
            );
            $stmt->execute();
            $stmt->close();

            // удаляем старые строки
            $stmt = $this->conn->prepare(
            "DELETE FROM order_line WHERE order_id = ?"
            );
            $stmt->bind_param('i', $orderId);
            $stmt->execute();$stmt->close();

            // вставляем новые строки
            $stmt = $this->conn->prepare(
            "INSERT INTO order_line (order_id, product_id, quantity_ordered, expected_price)
            VALUES (?, ?, ?, ?)"
            );
            foreach ($d['lines'] as $ln) {
                $stmt->bind_param('iiid',
                $orderId,
                $ln['product_id'],
                $ln['qty'],
                $ln['price']
                );
                $stmt->execute();
            }
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // === Удалить заявку и её строки
    public function delete(int $orderId): void {
        // сначала строки
        $stmt = $this->conn->prepare(
        "DELETE FROM order_line WHERE order_id = ?"
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $stmt->close();

        // потом сам заголовок
        $stmt = $this->conn->prepare(
        "DELETE FROM purchase_order WHERE order_id = ?"
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $stmt->close();
    }
    public function hasInvoice(int $orderId): bool {
        $stmt = $this->conn->prepare(
        "SELECT COUNT(*) AS cnt
        FROM invoice
        WHERE order_id = ?"
        );
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: ['cnt' => 0];
        $stmt->close();
        return (int)$row['cnt'] > 0;
    }
}
class Product {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ===  Получить все товары
    public function getAll(): array {
        $stmt = $this->conn->prepare(
        "SELECT product_id AS id, name, category, unit_of_measure
        FROM product"
        );
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // === Добавить новый товар
    public function create(string $name, string $category = null, string $unit = null): int {
        $stmt = $this->conn->prepare(
        "INSERT INTO product (name, category, unit_of_measure) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('sss', $name, $category, $unit);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    // === Удалить товар по ID
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare(
        "DELETE FROM product WHERE product_id = ?"
        );
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    // === Получить один товар по ID
    public function getById(int $id): array {
        $stmt = $this->conn->prepare(
        "SELECT product_id AS id, name, category, unit_of_measure
        FROM product
        WHERE product_id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res ?: [];
    }

    // === Обновить товар
    public function update(int $id, string $name, ?string $category, ?string $unit): bool {
        $stmt = $this->conn->prepare(
        "UPDATE product
        SET name = ?, category = ?, unit_of_measure = ?
        WHERE product_id = ?"
        );
        $stmt->bind_param('sssi', $name, $category, $unit, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
class Employee {
    private \mysqli $conn;
    public function __construct(\mysqli $conn) {
        $this->conn = $conn;
    }

    // === Список всех сотрудников
    public function getAll(): array {
        $res = $this->conn->query(
        "SELECT employee_id AS id, full_name, position
        FROM employee
        ORDER BY full_name"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // === Один сотрудник по ID
    public function getById(int $id): array {
        $stmt = $this->conn->prepare(
        "SELECT employee_id AS id, full_name, position
        FROM employee
        WHERE employee_id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();
        return $row;
    }

    // === Добавить нового
    public function create(string $name, string $position): bool {
        $stmt = $this->conn->prepare(
        "INSERT INTO employee (full_name, position) VALUES (?, ?)"
        );
        $stmt->bind_param('ss', $name, $position);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // === Обновить
    public function update(int $id, string $name, string $position): bool {
        $stmt = $this->conn->prepare(
        "UPDATE employee
        SET full_name = ?, position = ?
        WHERE employee_id = ?"
        );
        $stmt->bind_param('ssi', $name, $position, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // === Удалить по ID
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare(
        "DELETE FROM employee WHERE employee_id = ?"
        );
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}