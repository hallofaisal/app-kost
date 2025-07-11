<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Routing sederhana berbasis URL dan method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Routing utama (contoh awal, endpoint CRUD dan khusus akan ditambah)
if (preg_match('#^/api/penghuni#', $uri)) {
    require_once __DIR__ . '/../controllers/PenghuniController.php';
    $controller = new PenghuniController($pdo);
    $controller->handle($method, $uri);
    exit;
}
if (preg_match('#^/api/bayar#', $uri)) {
    require_once __DIR__ . '/../controllers/BayarController.php';
    $controller = new BayarController($pdo);
    $controller->handle($method, $uri);
    exit;
}
// ... endpoint lain akan ditambahkan di sini

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']); 