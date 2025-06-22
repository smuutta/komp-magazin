<?php

  $products = $products ?? [];

  $isEdit = isset($product);

  $action   = $isEdit ? 'product/update' : 'product/create';
  $id       = $isEdit ? $product['id']            : '';
  $name     = $isEdit ? $product['name']          : '';
  $category = $isEdit ? $product['category']      : '';
  $unit     = $isEdit ? $product['unit_of_measure']: '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">

  <style>
    body { font-family: Arial,sans-serif; max-width: 800px; margin: 20px auto; }
    h1,h2,h3 { margin-top: 1.4em; }
    form { margin-bottom: 1em; }
    form label { display: block; margin-bottom: 8px; }
    form input { padding: 4px; width: 100%; max-width: 300px; box-sizing: border-box; }
    .btn, button { padding: 6px 12px; margin-right: 6px; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 1em; }
    table th, table td { border: 1px solid #ccc; padding: 8px; }
    table th { background: #f9f9f9; text-align: left; }
    .success { background: #e0ffe2; color: #24803c; padding: 8px; margin-bottom: 16px; }
    .error   { background: #ffe0e0; color: #800000; padding: 8px; margin-bottom: 16px; }
  </style>
</head>
<body>


  <?php if ($isEdit): ?>
    <h2>Редактирование товара #<?= htmlspecialchars($id) ?></h2>
    <form method="post" action="index.php?route=<?= htmlspecialchars($action) ?>">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

      <label>Название:
        <input type="text" name="name" required value="<?= htmlspecialchars($name) ?>">
      </label>

      <label>Категория:
        <input type="text" name="category" value="<?= htmlspecialchars($category) ?>">
      </label>

      <label>Ед. измерения:
        <input type="text" name="unit" value="<?= htmlspecialchars($unit) ?>">
      </label>

      <button type="submit">Сохранить</button>
      <a class="btn" href="index.php?route=product/form">Отмена</a>
    </form>

  <?php else: ?>

    <h2>Управление товарами</h2>
<p>
  <a href="index.php" class="btn btn-primary">◀️ Главная</a>
</p>
    <?php if (isset($_GET['success'])): ?>
      <div class="success">
        Товар успешно <?= $_GET['success'] === 'updated' ? 'обновлён' : 'добавлен' ?>.
      </div>
    <?php elseif (!empty($_GET['error'])): ?>
      <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?route=product/create">
      <label>Название товара:
        <input type="text" name="name" required>
      </label>

      <label>Категория:
        <input type="text" name="category">
      </label>

      <label>Ед. измерения:
        <input type="text" name="unit" placeholder="шт, кг и т.д.">
      </label>

      <button type="submit">Добавить товар</button>
    </form>

    <h3>Существующие товары</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Название</th>
          <th>Категория</th>
          <th>Ед.изм.</th>
          <th>Действие</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($products) > 0): ?>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td><?= htmlspecialchars($p['unit_of_measure']) ?></td>
              <td>
                <a class="btn" href="index.php?route=product/view&id=<?= htmlspecialchars($p['id']) ?>">Просмотр</a>
                <a class="btn" href="index.php?route=product/edit&id=<?= htmlspecialchars($p['id']) ?>">Редактировать</a>
                <a class="btn" href="index.php?route=product/delete&id=<?= htmlspecialchars($p['id']) ?>"
                   onclick="return confirm('Удалить?')">Удалить</a>
              </td>
            </tr><?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">Товаров пока нет.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php endif; ?>
</body>
</html>