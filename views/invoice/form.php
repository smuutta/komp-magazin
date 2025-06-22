<?php
?>
<h2>Создать накладную</h2>
<p>
  <a href="index.php" class="btn btn-primary">◀️ Главная</a>
</p>
<form method="get" action="index.php">
  <input type="hidden" name="route" value="invoice/form">
  <label>Выбрать заявку для приёма:<br>
    <select name="order_id"
            onchange="this.form.submit()">
      <option value="0">— ручной ввод —</option>
      <?php foreach ($orders as $o): ?>
        <option value="<?= $o['id'] ?>"
          <?= isset($orderId) && $orderId==$o['id']?'selected':''?>>
          Заявка №<?= $o['id'] ?> от <?= $o['order_date']?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
</form>

<?php if (!$orderId): ?>
  <!-- ручной ввод числа строк -->
  <form method="get" action="index.php" style="margin-top:1em;">
    <input type="hidden" name="route" value="invoice/form">
    <label>Число строк:
      <input type="number" name="count" value="<?= $count ?>" min="1" style="width:60px;">
    </label>
    <button type="submit">Показать</button>
  </form>
<?php endif; ?>

<form action="index.php?route=invoice/create" method="post" style="margin-top:1em;">
  <?php if ($orderId): ?>
    <input type="hidden" name="order_id" value="<?= $orderId ?>">
  <?php else: ?>
    <input type="hidden" name="count"    value="<?= $count ?>">
  <?php endif; ?>

  <label>Сотрудник:<br>
    <?php if ($orderId): ?>
      <input disabled value="<?= htmlspecialchars(
        array_values(array_filter($emps, fn($e)=>$e['id']==$order['employee_id']))[0]['name']
      ) ?>">
      <input type="hidden" name="employee_id" value="<?= $order['employee_id'] ?>">
    <?php else: ?>
      <select name="employee_id" required>
        <option value="">— выберите —</option>
        <?php foreach ($emps as $e): ?>
          <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>
  </label><br><br>

  <label>Поставщик:<br>
    <?php if ($orderId): ?>
      <input disabled value="<?= htmlspecialchars(
        array_values(array_filter($sups, fn($s)=>$s['id']==$order['supplier_id']))[0]['name']
      ) ?>">
      <input type="hidden" name="supplier_id" value="<?= $order['supplier_id'] ?>">
    <?php else: ?>
      <select name="supplier_id" required>
        <option value="">— выберите —</option>
        <?php foreach ($sups as $s): ?>
          <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>
  </label><br><br>

  <label>Дата накладной:<br>
    <?php if ($orderId): ?>
      <input disabled value="<?= htmlspecialchars($order['order_date']) ?>">
      <input type="hidden" name="invoice_date" value="<?= $order['order_date'] ?>">
    <?php else: ?>
      <input type="date" name="invoice_date" value="<?= date('Y-m-d') ?>" required>
    <?php endif; ?>
  </label><br><br>

  <h3>Строки накладной</h3>
  <?php for ($i = 0; $i < $count; $i++): ?>
    <?php 
      // если приём заявки — рисуем ровно столько строк, сколько в $lines
      if ($orderId) {
        $ln = $lines[$i] ?? ['product_id'=>0,'qty'=>0];
      } else {
        $ln = ['product_id'=>0,'qty'=>0];
      }
    ?>
    <div style="margin-bottom:8px;">
      <select name="lines[<?= $i ?>][product_id]" 
              <?= $orderId?'disabled':''?> >
        <option value="">— товар —</option>
        <?php foreach ($prods as $p): ?>
          <option value="<?= $p['id'] ?>"
            <?= $p['id']==$ln['product_id']?'selected':''?>>
            <?= htmlspecialchars($p['name']) ?> (на складе: <?= $p['stock'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <input type="number"
             name="lines[<?= $i ?>][qty]"
             min="1"
             placeholder="кол-во"
             style="width:80px;"
             value="<?= $ln['qty'] ?>"
             <?= $orderId?'readonly':''?>>
    </div>
  <?php endfor; ?>

  <button type="submit">
    <?= $orderId ? 'Принять заявку и сохранить' : 'Сохранить накладную' ?>
  </button>
</form>