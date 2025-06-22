<?php
// фронт-контроллер
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/model.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {

    // Главная
    case 'home':
    default:
        include __DIR__ . '/views/home.php';
        break;


    // ────── Product CRUD ──────
    case 'product/form':
        require_once __DIR__ . '/controllers/product_controller.php';
        (new ProductController($conn))->showForm();
        break;
    case 'product/create':
        require_once __DIR__ . '/controllers/product_controller.php';
        (new ProductController($conn))->create();
        break;
    case 'product/view':
        require_once __DIR__ . '/controllers/product_controller.php';
        (new ProductController($conn))->view();
        break;
    case 'product/edit':
        require_once __DIR__ . '/controllers/product_controller.php';
        (new ProductController($conn))->editForm();
        break;
    case 'product/update':
        require_once __DIR__ . '/controllers/product_controller.php';
        (new ProductController($conn))->update();
        break;
    case 'product/delete':
        require_once __DIR__ . '/controllers/product_controller.php';
        (new ProductController($conn))->delete();
        break;


    // ────── Employee CRUD ──────
    case 'employee/form':
        require_once __DIR__ . '/controllers/employee_controller.php';
        (new EmployeeController($conn))->form();
        break;
    case 'employee/create':
        require_once __DIR__ . '/controllers/employee_controller.php';
        (new EmployeeController($conn))->create();
        break;
    case 'employee/view':
        require_once __DIR__ . '/controllers/employee_controller.php';
        (new EmployeeController($conn))->view();
        break;
    case 'employee/edit':
        require_once __DIR__ . '/controllers/employee_controller.php';
        (new EmployeeController($conn))->edit();
        break;
    case 'employee/update':
        require_once __DIR__ . '/controllers/employee_controller.php';
        (new EmployeeController($conn))->update();
        break;
    case 'employee/delete':
        require_once __DIR__ . '/controllers/employee_controller.php';
        (new EmployeeController($conn))->delete();
        break;


    // ────── Zayavka (PurchaseOrder) CRUD ──────
    case 'zayavka/form':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->form();
        break;
    case 'zayavka/create':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->create();
        break;
    case 'zayavka/view':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->view();
        break;
    case 'zayavka/edit':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->edit();
        break;
    case 'zayavka/update':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->update();
        break;
    case 'zayavka/delete':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->delete();
        break;
    case 'zayavka/success':
        require_once __DIR__ . '/controllers/zayavka_controller.php';
        (new ZayavkaController($conn))->success();
        break;


    // ────── Invoice CRUD ──────
    case 'invoice/form':
        require_once __DIR__ . '/controllers/invoice_controller.php';
        (new InvoiceController($conn))->showForm();
        break;
    case 'invoice/create':
        require_once __DIR__ . '/controllers/invoice_controller.php';
        (new InvoiceController($conn))->create();
        break;
    case 'invoice/success':
        require_once __DIR__ . '/controllers/invoice_controller.php';
        (new InvoiceController($conn))->success();
        break;


    // ────── Sale (Чек) CRUD ──────
    case 'sale/form':
        require_once __DIR__ . '/controllers/sale_controller.php';
        (new SaleController($conn))->showForm();
        break;
    case 'sale/create':require_once __DIR__ . '/controllers/sale_controller.php';
        (new SaleController($conn))->create();
        break;
    case 'sale/success':
        require_once __DIR__ . '/controllers/sale_controller.php';
        (new SaleController($conn))->success();
        break;


    // ────── Report ──────
    case 'report':
        require_once __DIR__ . '/controllers/report_controller.php';
        (new ReportController($conn))->index();
        break;
}