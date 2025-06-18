-- schema.sql  (IDEF1X → физическая модель)
-- ********************************************************
DROP DATABASE IF EXISTS komputernyy_magazin;
CREATE DATABASE komputernyy_magazin CHARACTER SET utf8mb4;
USE komputernyy_magazin;

-- 1. Поставщики ------------------------------------------------
CREATE TABLE supplier (
    supplier_id   INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    contact_info  VARCHAR(150),
    legal_details TEXT
) ENGINE=InnoDB;

-- 2. Сотрудники ------------------------------------------------
CREATE TABLE employee (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(150) NOT NULL,
    position    ENUM('manager','warehouse_dispatcher','cashier') NOT NULL
) ENGINE=InnoDB;

-- 3. Товары ----------------------------------------------------
CREATE TABLE product (
    product_id     INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(150) NOT NULL,
    category       VARCHAR(100),
    unit_of_measure VARCHAR(30)
) ENGINE=InnoDB;

-- 4. Заявки ----------------------------------------------------
CREATE TABLE purchase_order (
    order_id    INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    employee_id INT NOT NULL,             -- менеджер-инициатор
    order_date  DATE NOT NULL,
    status      ENUM('new','sent','partly_received','received','cancelled') DEFAULT 'new',
    FOREIGN KEY (supplier_id) REFERENCES supplier(supplier_id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
) ENGINE=InnoDB;

-- 4a. Строки заявки -------------------------------------------
CREATE TABLE order_line (
    order_id   INT,
    product_id INT,
    quantity_ordered  INT    NOT NULL,
    expected_price    DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id)   REFERENCES purchase_order(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
) ENGINE=InnoDB;

-- 5. Приходные накладные --------------------------------------
CREATE TABLE invoice (
    invoice_id  INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    employee_id INT NOT NULL,            -- диспетчер склада
    order_id    INT,
    invoice_date DATE NOT NULL,
    status      ENUM('checking','stored','closed') DEFAULT 'checking',
    FOREIGN KEY (supplier_id) REFERENCES supplier(supplier_id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (order_id)    REFERENCES purchase_order(order_id)
) ENGINE=InnoDB;

-- 5a. Строки накладной ----------------------------------------
CREATE TABLE invoice_line (
    invoice_id       INT,
    product_id       INT,
    quantity_received INT    NOT NULL,
    actual_price      DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (invoice_id, product_id),
    FOREIGN KEY (invoice_id) REFERENCES invoice(invoice_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
) ENGINE=InnoDB;

-- 6. Чеки (продажи) -------------------------------------------
CREATE TABLE sale (
    sale_id     INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,            -- кассир
    sale_date   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
) ENGINE=InnoDB;

-- 6a. Строки чека ---------------------------------------------
CREATE TABLE sale_line (
    sale_id      INT,
    product_id   INT,
    quantity_sold INT    NOT NULL,
    sale_price    DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (sale_id, product_id),
    FOREIGN KEY (sale_id)    REFERENCES sale(sale_id)     ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
) ENGINE=InnoDB;

-- Индексы для ускорения поиска по FK
CREATE INDEX idx_order_supplier   ON purchase_order(supplier_id);
CREATE INDEX idx_invoice_supplier ON invoice(supplier_id);
CREATE INDEX idx_sale_date        ON sale(sale_date);