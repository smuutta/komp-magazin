<?php
  $orders = $orders ?? [];
  $isEdit = isset($order['id']);
  $count  = $count  ?? 1;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>ИС «Компьютерный магазин» — Управление заявками</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; }
    h1,h2,h3 { margin: 1em 0; }
    form { margin-bottom: 1.5em; }
    label { display: block; margin-bottom: 8px; }
    select, input[type="date"], input[type="number"] {
      padding: 4px; width: 100%; max-width: 300px; box-sizing: border-box;
      margin-top: 4px;
    }
    button, .btn { padding: 6px 12px; margin-right: 8px; }
    .responsive { overflow-x: auto; margin-bottom: 1em; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 1em; }
    table th, table td {
      border: 1px solid #ccc; padding: 8px; white-space: nowrap;
    }
    #lines-table { table-layout: fixed; }
    #lines-table col:first-child  { width: 60%; }
    #lines-table col:nth-child(2) { width: 20%; }
    #lines-table col:nth-child(3) { width: 20%; }
    .success { background: #e0ffe2; color: #24803c; padding: 8px; margin-bottom: 16px; }
    .error   { background: #ffe0e0; color: #800000; padding: 8px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <h2>Управление заявками</h2>
  <p>
  <a href="index.php" class="btn btn-primary">◀️ Главная</a>
</p>
<?php if (!empty($_GET['error']) && $_GET['error'] === 'hasInvoice'): ?>
  <div class="error">
    Невозможно удалить заявку: для неё уже создана хотя бы одна накладная.
  </div>
<?php endif; ?>

  <?php if ($isEdit): ?>
    <!-- ====== Редактирование заявки ====== -->
    <h3>Редактирование заявки #<?= htmlspecialchars($order['id']) ?></h3>
    <form method="post" action="index.php?route=zayavka/update">
      <input type="hidden" name="id" value="<?= htmlspecialchars($order['id']) ?>">

      <label>Поставщик:
        <select name="supplier_id">
          <?php foreach ($sups as $s): ?>
            <option value="<?= $s['id'] ?>"
              <?= $s['id'] == $order['supplier_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Сотрудник:
        <select name="employee_id">
          <?php foreach ($emps as $e): ?>
            <option value="<?= $e['id'] ?>"
              <?= $e['id'] == $order['employee_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($e['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Дата заказа:
        <input type="date" name="order_date"
               value="<?= htmlspecialchars($order['order_date']) ?>">
      </label>

      <h3>Строки заявки</h3>
      <div class="responsive">
        <table id="lines-table">
          <colgroup><col><col><col></colgroup>
          <thead>
            <tr><th>Товар</th><th>Кол-во</th><th>Цена</th></tr>
          </thead>
          <tbody>
            <?php foreach ($order['lines'] as $i => $ln): ?>
              <tr>
                <td>
                  <select name="lines[<?= $i ?>][product_id]">
                    <?php foreach ($prods as $p): ?>
                      <option value="<?= $p['id'] ?>"
                        <?= $p['id'] == $ln['product_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td>
                  <input type="number" name="lines[<?= $i ?>][qty]" min="1"
                         value="<?= htmlspecialchars($ln['qty']) ?>">
                </td>
                <td>
                  <input type="number" step="0.01" name="lines[<?= $i ?>][price]" min="0"
                         value="<?= htmlspecialchars($ln['price']) ?>">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <button type="submit">Сохранить</button>
      <a class="btn" href="index.php?route=zayavka/form">Отмена</a>
    </form>

  <?php else: ?>
    <!-- ====== Создание новой заявки ====== -->
    <h3>Создать новую заявку</h3><?php if (isset($_GET['success'])): ?>
      <div class="success">Заявка успешно создана.</div>
    <?php elseif (!empty($_GET['error'])): ?>
      <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- Форма выбора числа строк -->
    <form method="get" action="index.php" style="margin-bottom:1em;">
      <input type="hidden" name="route" value="zayavka/form">
      <label>Количество позиций:
        <input type="number" name="count" min="1" value="<?= htmlspecialchars($count) ?>">
      </label>
      <button type="submit">Обновить</button>
    </form>

    <!-- Форма создания -->
    <form method="post" action="index.php?route=zayavka/create">
      <label>Поставщик:
        <select name="supplier_id">
          <?php foreach ($sups as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Сотрудник:
        <select name="employee_id">
          <?php foreach ($emps as $e): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Дата заказа:
        <input type="date" name="order_date" value="<?= date('Y-m-d') ?>">
      </label>

      <h3>Строки заявки</h3>
      <div class="responsive">
        <table id="lines-table">
          <colgroup><col><col><col></colgroup>
          <thead>
            <tr><th>Товар</th><th>Кол-во</th><th>Цена</th></tr>
          </thead>
          <tbody>
            <?php for ($i = 0; $i < $count; $i++): ?>
              <tr>
                <td>
                  <select name="lines[<?= $i ?>][product_id]">
                    <?php foreach ($prods as $p): ?>
                      <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td>
                  <input type="number" name="lines[<?= $i ?>][qty]" min="1">
                </td>
                <td>
                  <input type="number" step="0.01" name="lines[<?= $i ?>][price]" min="0">
                </td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <button type="submit">Создать заявку</button>
    </form>
  <?php endif; ?>

  <!-- ====== Список всех заявок ====== -->
  <h3>Существующие заявки</h3>
  <div class="responsive">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Поставщик</th><th>Сотрудник</th>
          <th>Дата</th><th>Статус</th><th>Действие</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($orders) > 0): ?>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><?= htmlspecialchars($o['id']) ?></td>
              <td><?= htmlspecialchars($o['supplier_id']) ?></td>
              <td><?= htmlspecialchars($o['employee_id']) ?></td>
              <td><?= htmlspecialchars($o['order_date']) ?></td>
              <td><?= htmlspecialchars($o['status']) ?></td>
              <td>
                <a href="index.php?route=zayavka/view&id=<?= $o['id'] ?>">Просмотр</a>
                <a href="index.php?route=zayavka/edit&id=<?= $o['id'] ?>">Редактировать</a>
                <a href="index.php?route=zayavka/delete&id=<?= $o['id'] ?>"
                   onclick="return confirm('Удалить заявку?')">Удалить</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">Заявок пока нет.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>