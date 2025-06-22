<h2>Детали товара #<?= htmlspecialchars($product['id']) ?></h2>
<ul>
  <li><strong>Название:</strong> <?= htmlspecialchars($product['name']) ?></li>
  <li><strong>Категория:</strong> <?= htmlspecialchars($product['category']) ?></li>
  <li><strong>Ед. измерения:</strong> <?= htmlspecialchars($product['unit_of_measure']) ?></li>
</ul>
<p>
  <a href="index.php?route=product/edit&id=<?= $product['id'] ?>">Редактировать</a> |
  <a href="index.php?route=product/form">Назад к списку</a>
</p>