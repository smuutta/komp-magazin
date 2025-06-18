-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 18 2025 г., 21:31
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `komputernyy_magazin`
--

-- --------------------------------------------------------

--
-- Структура таблицы `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `position` enum('manager','warehouse_dispatcher','cashier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `employee`
--

INSERT INTO `employee` (`employee_id`, `full_name`, `position`) VALUES
(1, 'Иван Менеджер', 'manager'),
(2, 'Ольга Склад', 'warehouse_dispatcher'),
(3, 'Пётр Кассир', 'cashier');

-- --------------------------------------------------------

--
-- Структура таблицы `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `status` enum('pending','checked','closed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `supplier_id`, `employee_id`, `order_id`, `invoice_date`, `status`) VALUES
(1, 1, 2, 3, '2025-06-13', 'pending'),
(2, 1, 1, 7, '2025-08-12', 'pending'),
(3, 1, 1, 8, '2025-06-18', 'pending'),
(4, 1, 2, 3, '2025-06-18', 'pending');

-- --------------------------------------------------------

--
-- Структура таблицы `invoice_line`
--

CREATE TABLE `invoice_line` (
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_received` int(11) NOT NULL,
  `actual_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `invoice_line`
--

INSERT INTO `invoice_line` (`invoice_id`, `product_id`, `quantity_received`, `actual_price`) VALUES
(1, 1, 52, 15125.00),
(2, 2, 15, 32000.00),
(3, 2, 12, 35000.00),
(4, 1, 12, 52355.00);

-- --------------------------------------------------------

--
-- Структура таблицы `order_line`
--

CREATE TABLE `order_line` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `expected_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_line`
--

INSERT INTO `order_line` (`order_id`, `product_id`, `quantity_ordered`, `expected_price`) VALUES
(6, 1, 8, 67655.00),
(7, 2, 15, 35000.00),
(8, 2, 12, 35000.00);

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit_of_measure` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`product_id`, `name`, `category`, `unit_of_measure`) VALUES
(1, 'Процессор Intel i5', 'CPU', 'шт'),
(2, 'Видеокарта GTX 1650', 'GPU', 'шт');

-- --------------------------------------------------------

--
-- Структура таблицы `purchase_order`
--

CREATE TABLE `purchase_order` (
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `status` enum('created','sent','received','cancelled') DEFAULT 'created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `purchase_order`
--

INSERT INTO `purchase_order` (`order_id`, `supplier_id`, `employee_id`, `order_date`, `status`) VALUES
(1, 1, 1, '2025-06-19', ''),
(2, 1, 1, '2025-06-19', ''),
(3, 1, 1, '2025-06-19', ''),
(4, 1, 1, '2025-06-19', ''),
(5, 1, 1, '2025-06-19', ''),
(6, 1, 1, '2025-06-20', 'created'),
(7, 1, 1, '2025-08-07', 'created'),
(8, 1, 1, '2025-06-25', 'created');

-- --------------------------------------------------------

--
-- Структура таблицы `sale`
--

CREATE TABLE `sale` (
  `sale_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `sale_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `sale`
--

INSERT INTO `sale` (`sale_id`, `employee_id`, `sale_date`) VALUES
(1, 1, '2025-06-18'),
(2, 3, '2025-06-18'),
(3, 1, '2025-06-18'),
(7, 1, '2025-06-18');

-- --------------------------------------------------------

--
-- Структура таблицы `sale_line`
--

CREATE TABLE `sale_line` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_sold` int(11) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `sale_line`
--

INSERT INTO `sale_line` (`sale_id`, `product_id`, `quantity_sold`, `sale_price`) VALUES
(1, 1, 4, 500.00),
(2, 1, 32, 7000.00),
(3, 1, 23, 6455.00),
(7, 2, 7, 40000.00);

-- --------------------------------------------------------

--
-- Структура таблицы `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `contact_info` varchar(150) DEFAULT NULL,
  `legal_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `name`, `contact_info`, `legal_details`) VALUES
(1, 'ООО \"ТехноПлюс\"', '8-800-123-4567', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`);

--
-- Индексы таблицы `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_invoice_supplier` (`supplier_id`);

--
-- Индексы таблицы `invoice_line`
--
ALTER TABLE `invoice_line`
  ADD PRIMARY KEY (`invoice_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `order_line`
--
ALTER TABLE `order_line`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Индексы таблицы `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_order_supplier` (`supplier_id`);

--
-- Индексы таблицы `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_sale_date` (`sale_date`);

--
-- Индексы таблицы `sale_line`
--
ALTER TABLE `sale_line`
  ADD PRIMARY KEY (`sale_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `sale`
--
ALTER TABLE `sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`order_id`);

--
-- Ограничения внешнего ключа таблицы `invoice_line`
--
ALTER TABLE `invoice_line`
  ADD CONSTRAINT `invoice_line_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_line_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Ограничения внешнего ключа таблицы `order_line`
--
ALTER TABLE `order_line`
  ADD CONSTRAINT `order_line_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_line_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD CONSTRAINT `purchase_order_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `purchase_order_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Ограничения внешнего ключа таблицы `sale`
--
ALTER TABLE `sale`
  ADD CONSTRAINT `sale_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Ограничения внешнего ключа таблицы `sale_line`
--
ALTER TABLE `sale_line`
  ADD CONSTRAINT `sale_line_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`sale_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_line_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
