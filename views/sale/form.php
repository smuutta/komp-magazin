<?php
?>
<h2>Продажа товара (чек)</h2>
<p>
  <a href="index.php" class="btn btn-primary">◀️ Главная</a>
</p>
<?php if (!empty($_GET['error'])): ?>
  <div style="color:red; margin-bottom:1em;">
    <?= htmlspecialchars($_GET['error']) ?>
  </div>
<?php endif; ?>

<form action="index.php?route=sale/create" method="post">
    <label>Сотрудник:<br>
      <select name="employee_id" required>
        <option value="">— выберите —</option>
        <?php foreach ($emps as $e): ?>
          <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Товар:<br>
      <select name="product_id" required>
        <option value="">— выберите —</option>
        <?php foreach ($prods as $p): ?>
          <option value="<?= $p['id'] ?>">
            <?= htmlspecialchars($p['name']) ?> (на складе: <?= $p['stock'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Количество:<br>
      <input type="number" name="quantity" min="1" required>
    </label><br><br>

    <label>Цена за единицу:<br>
      <input type="text" name="price" required>
    </label><br><br>

    <label>Дата продажи:<br>
      <input type="date" name="sale_date" value="<?= date('Y-m-d') ?>" required>
    </label><br><br>

    <button type="submit">Пробить чек</button>
</form>