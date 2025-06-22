<h2>Сотрудник #<?= $emp['id'] ?></h2>
<ul>
  <li>ФИО: <?= htmlspecialchars($emp['full_name']) ?></li>
  <li>Должность: <?= htmlspecialchars($emp['position']) ?></li>
</ul>
<p><a href="index.php?route=employee/form">Назад к списку</a></p>
<p>
  <a href="index.php" class="btn btn-primary">◀️ Главная</a>
</p>