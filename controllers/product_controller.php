<?php

class ProductController
{
    private $conn;
    private $m;

    // === Конструктор ===
    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->m    = new Product($conn);
    }

    // === Показать форму управления товарами ===
    public function showForm()
    {
        $products = $this->m->getAll();
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/product/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === Добавить новый товар ===
    public function create()
    {
        $name     = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $unit     = trim($_POST['unit'] ?? '');

        if ($name === '') {
            $_GET['error'] = 'Название не может быть пустым';
            return $this->showForm();
        }

        $this->m->create($name, $category ?: null, $unit ?: null);
        header("Location: index.php?route=product/form&success=1");
        exit;
    }

    // === Удалить товар ===
    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->m->delete($id);
        }
        header("Location: index.php?route=product/form");
        exit;
    }

    // === Показать детали товара ===
    public function view()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?route=product/form");
            exit;
        }

        $product = $this->m->getById($id);
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/product/view.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === Показать форму редактирования ===
    public function editForm()
    {
        $id       = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?route=product/form");
            exit;
        }

        $product  = $this->m->getById($id);
        $products = $this->m->getAll(); // чтобы таблица не ломалась

        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/product/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // === Обработать обновление ===
    public function update()
    {
        $id       = (int)($_POST['id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $unit     = trim($_POST['unit'] ?? '');

        if ($id <= 0 || $name === '') {
            $_GET['error'] = 'Неверные данные для обновления';
            return $this->editForm();
        }

        $this->m->update($id, $name, $category ?: null, $unit ?: null);
        header("Location: index.php?route=product/form&success=updated");
        exit;
    }
}
