<?php
  $employees = $employees ?? [];
  $isEdit    = isset($emp);
?>
<h2>Сотрудники</h2>
<p>
  <a href="index.php" class="btn btn-primary">◀️ Главная</a>
</p>
<?php if ($isEdit): ?>
  <h3>Редактировать сотрудника #<?= $emp['id'] ?></h3>
<?php else: ?>
  <h3>Добавить нового сотрудника</h3>
<?php endif; ?>

<form method="post" action="index.php?route=employee/<?= $isEdit ? 'update' : 'create' ?>">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $emp['id'] ?>">
  <?php endif; ?>

  <label>ФИО:<br>
    <input name="full_name" required
           value="<?= $isEdit ? htmlspecialchars($emp['full_name']) : '' ?>">
  </label>
  <?php
  // массив «ключ» => «читаемое название»
  $positions = [
    'manager'              => 'Менеджер',
    'warehouse_dispatcher' => 'Складской диспетчер',
    'cashier'              => 'Кассир'
  ];
  // текущая выбранная (в режиме редактирования)
  $currentPos = $isEdit ? $emp['position'] : '';
?>
<label>Должность:<br>
  <select name="position" required>
    <?php foreach($positions as $value => $label): ?>
      <option value="<?= $value?>"
        <?= $value === $currentPos ? 'selected' : ''?>>
        <?= $label ?>
      </option>
    <?php endforeach; ?>
  </select>
</label>

  <button type="submit"><?= $isEdit ? 'Сохранить' : 'Добавить' ?></button>
  <?php if ($isEdit): ?>
    <a href="index.php?route=employee/form">Отмена</a>
  <?php endif; ?>
</form>

<h3>Список сотрудников</h3>
<table border="1" cellpadding="4" cellspacing="0">
  <tr><th>ID</th><th>ФИО</th><th>Должность</th><th>Действия</th></tr>
  <?php foreach($employees as $e): ?>
    <tr>
      <td><?= $e['id'] ?></td>
      <td><?= htmlspecialchars($e['full_name']) ?></td>
      <td><?= htmlspecialchars($e['position']) ?></td>
      <td>
        <a href="index.php?route=employee/view&id=<?= $e['id'] ?>">Просмотр</a>
        <a href="index.php?route=employee/edit&id=<?= $e['id'] ?>">Редактировать</a>
        <a href="index.php?route=employee/delete&id=<?= $e['id'] ?>"
           onclick="return confirm('Удалить сотрудника?')">Удалить</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>