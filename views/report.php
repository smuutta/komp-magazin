<?php
// views/report.php
?>
<h2>Отчёт по продажам</h2>

<?php if (empty($rows)): ?>
  <p>Пока нет ни одной продажи.</p>
<?php else: ?>
  <div style="overflow-x:auto;">
    <table
      border="1"
      cellpadding="6"
      cellspacing="0"
      style="border-collapse:collapse; width:100%; min-width:600px;"
    >
      <thead>
        <tr style="background:#f0f0f0;">
          <th>Чек&nbsp;№</th>
          <th>Дата</th>
          <th>Сотрудник</th>
          <th>Товар</th>
          <th>Кол-во</th>
          <th>Цена</th>
          <th>Сумма</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['sale_id']) ?></td>
            <td><?= htmlspecialchars($r['sale_date']) ?></td>
            <td><?= htmlspecialchars($r['employee']) ?></td>
            <td><?= htmlspecialchars($r['product']) ?></td>
            <td style="text-align:right;"><?= htmlspecialchars($r['qty']) ?></td>
            <td style="text-align:right;"><?= htmlspecialchars(number_format($r['price'],2,',','')) ?></td>
            <td style="text-align:right;"><?= htmlspecialchars(number_format($r['total_line'],2,',','')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="font-weight:bold; background:#f9f9f9;">
          <td colspan="6" style="text-align:right;">ИТОГО:</td>
          <td style="text-align:right;"><?= htmlspecialchars(number_format($grandTotal,2,',','')) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
<?php endif; ?>

<p style="margin-top:1em;">
  <a href="index.php">← Назад в меню</a>
</p>