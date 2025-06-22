<?php
require_once __DIR__ . '/../model.php';

class EmployeeController {
    private \mysqli $conn;
    private Employee $m;

    public function __construct(\mysqli $conn) {
        $this->conn = $conn;
        $this->m    = new Employee($conn);
    }

    // Показать список + форма добавления
    public function form() {
        $employees = $this->m->getAll();
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/employee/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // Создать
    public function create() {
        $name     = trim($_POST['full_name']  ?? '');
        $position = trim($_POST['position']   ?? '');
        if ($name !== '' && $position !== '') {
            $this->m->create($name, $position);
            header("Location: index.php?route=employee/form&success=created");
        } else {
            header("Location: index.php?route=employee/form&error=invalid");
        }
        exit;
    }

    // Просмотр
    public function view() {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) header("Location: index.php?route=employee/form");
        $emp = $this->m->getById($id);
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/employee/view.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // Форма редактирования
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) header("Location: index.php?route=employee/form");
        $emp       = $this->m->getById($id);
        $employees = $this->m->getAll();  // если нужен список
        include __DIR__ . '/../partials/header.php';
        include __DIR__ . '/../views/employee/form.php';
        include __DIR__ . '/../partials/footer.php';
    }

    // Обновить
    public function update() {
        $id       = (int)($_POST['id'] ?? 0);
        $name     = trim($_POST['full_name']  ?? '');
        $position = trim($_POST['position']   ?? '');
        if ($id && $name !== '' && $position !== '') {
            $this->m->update($id, $name, $position);
            header("Location: index.php?route=employee/form&success=updated");
        } else {
            header("Location: index.php?route=employee/edit&id={$id}&error=invalid");
        }
        exit;
    }

    // Удалить
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->m->delete($id);
            header("Location: index.php?route=employee/form&success=deleted");
        } else {
            header("Location: index.php?route=employee/form&error=invalid");
        }
        exit;
    }
}