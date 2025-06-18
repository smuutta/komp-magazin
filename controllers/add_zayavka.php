<?php
include("../config/db.php");

$data = $_POST['data'];
$postavshchik_id = $_POST['postavshchik_id'];
$tovar_id = $_POST['tovar_id'];
$kolvo = $_POST['kolvo'];
$cena = $_POST['cena'];

$conn->query("INSERT INTO zayavki (data, postavshchik_id) VALUES ('$data', $postavshchik_id)");
$zayavka_id = $conn->insert_id;

$conn->query("INSERT INTO pozicii_zayavki (zayavka_id, tovar_id, kolvo, cena)
              VALUES ($zayavka_id, $tovar_id, $kolvo, $cena)");

echo "Заявка успешно добавлена! <a href='../index.php'>На главную</a>";
?>