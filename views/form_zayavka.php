<?php include("../config/db.php"); ?>
<h2>Создание заявки</h2>
<form action="../controllers/add_zayavka.php" method="post">
    <label>Дата заявки:</label>
    <input type="date" name="data" required><br><br>

    <label>Поставщик:</label>
    <select name="postavshchik_id">
        <?php
        $res = $conn->query("SELECT * FROM postavshchiki");
        while($row = $res->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['naimenovanie']}</option>";
        }
        ?>
    </select><br><br>

    <label>Товар:</label>
    <select name="tovar_id">
        <?php
        $res = $conn->query("SELECT * FROM tovary");
        while($row = $res->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['naimenovanie']}</option>";
        }
        ?>
    </select><br><br>

    <label>Количество:</label>
    <input type="number" name="kolvo" required><br><br>

    <label>Цена:</label>
    <input type="number" name="cena" step="0.01" required><br><br>

    <input type="submit" value="Создать заявку">
</form>