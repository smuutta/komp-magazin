<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Компьютерный магазин</title>
    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 440px;
            margin: 50px auto 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 16px #d1d1d1;
            padding: 28px 36px 32px 36px;
        }
        h1 {
            text-align: center;
            color: #273c75;
            margin-bottom: 30px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin-bottom: 20px;
        }
        ul li a {
            display: block;
            background: #2979ff;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            padding: 12px;
            border-radius: 7px;
            text-align: center;
            transition: background 0.2s;
            box-shadow: 0 2px 6px #eee;
        }
        ul li a:hover {
            background: #1565c0;
        }
        .success {
            background: #e0ffe2;
            color: #24803c;
            border: 1px solid #99dbad;
            padding: 10px 0;
            border-radius: 6px;
            margin-bottom: 16px;
            text-align: center;
        }
        .title {
            margin-top: 0;
        }
        .footer {
            text-align: center;
            margin-top: 36px;
            color: #a0a0a0;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="title">ИС «Компьютерный магазин»</h1>

    <?php
    if (isset($_GET['success'])) {
        if ($_GET['success'] == 1) {
            echo "<div class='success'>Заявка успешно создана!</div>";
        } elseif ($_GET['success'] == "sale_ok") {
            echo "<div class='success'>Чек пробит успешно!</div>";
        }
    }
    ?>

    <ul>
        <li><a href="views/form_zayavka.php">Создать заявку</a></li>
        <li><a href="views/form_invoice.php">Оприходовать накладную</a></li>
        <li><a href="views/form_sale.php">Продажа товара (чек)</a></li>
        <li><a href="views/report.php">Отчёт по продажам</a></li>
    </ul>
</div>
<div class="footer">
    &copy; <?php echo date('Y'); ?> Курсовая работа Харьков Андрей ПИвУКИС-23 — <?php echo $_SERVER['SERVER_NAME'] ?? "localhost"; ?>
</div>
</body>
</html>