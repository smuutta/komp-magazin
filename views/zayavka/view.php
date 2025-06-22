<?php

  $order['lines'] = $order['lines'] ?? [];


?>
<h2>Заявка #<?= htmlspecialchars($order['id']) ?></h2>
<ul>
  <li>Поставщик: <?= htmlspecialchars($order['supplier_id']) ?></li>
  <li>Сотрудник: <?= htmlspecialchars($order['employee_id']) ?></li>
  <li>Дата: <?= htmlspecialchars($order['order_date']) ?></li>
  <li>Статус: <?= htmlspecialchars($order['status']) ?></li>
</ul>

<h3>Строки заявки</h3>
<table border="1" cellpadding="4" cellspacing="0">
  <tr><th>Товар</th><th>Кол-во</th><th>Цена</th></tr>

  <?php if (count($order['lines']) > 0): ?>
    <?php foreach ($order['lines'] as $ln): ?>
      <tr>
        <td><?= htmlspecialchars($ln['product_id']) ?></td>
        <td><?= htmlspecialchars($ln['qty']) ?></td>
        <td><?= htmlspecialchars($ln['price']) ?></td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="3">Нет позиций в этой заявке.</td></tr>
  <?php endif; ?>
</table>

<p><a href="index.php?route=zayavka/form">Назад к списку</a></p>