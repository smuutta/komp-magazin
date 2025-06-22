<?php
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ИС «Компьютерный магазин»</title>
    <style>
        body { background: #f5f5f5; font-family: Arial, sans-serif; }
        .container { width: 440px; margin: 50px auto; background: #fff; border-radius: 12px;
                     box-shadow: 0 0 16px #d1d1d1; padding: 28px 36px; }
        h1 { text-align: center; color: #273c75; margin-bottom: 30px; }
        ul { list-style: none; padding: 0; }
        ul li { margin-bottom: 20px; }
        ul li a { display: block; background: #2979ff; color: #fff; text-decoration: none;
                  font-size: 18px; padding: 12px; border-radius: 7px; text-align: center;
                  transition: background 0.2s; box-shadow: 0 2px 6px #eee; }
        ul li a:hover { background: #1565c0; }
        .success { background: #e0ffe2; color: #24803c; border: 1px solid #99dbad;
                   padding: 10px; border-radius: 6px; margin-bottom: 16px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>ИС «Компьютерный магазин»</h1>

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] == 1): ?>
            <div class="success">Заявка успешно создана!</div>
        <?php elseif ($_GET['success'] === 'sale_ok'): ?>
            <div class="success">Чек пробит успешно!</div>
        <?php endif; ?>
    <?php endif; ?>