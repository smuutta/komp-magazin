<?php
// views/home.php

include __DIR__ . '/../partials/header.php';
?>
<ul>
    <li><a href="index.php?route=zayavka/form">Создать заявку</a></li>
    <li><a href="index.php?route=invoice/form">Оприходовать накладную</a></li>
    <li><a href="index.php?route=sale/form">Продажа товара (чек)</a></li>
    <li><a href="index.php?route=report">Отчёт по продажам</a></li>
    <li><a href="index.php?route=product/form">Управление товарами</a></li>
    <li><a href="index.php?route=employee/form">Управление сотрудниками</a></li>
</ul>
<?php
include __DIR__ . '/../partials/footer.php';
