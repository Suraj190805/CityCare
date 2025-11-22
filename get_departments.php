<?php
// get_departments.php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT code, name FROM departments ORDER BY name ASC");
    $rows = $stmt->fetchAll();
    if (empty($rows)) {
        // fallback default list (keeps front-end working even if DB table missing)
        $rows = [
            ['code'=>'road','name'=>'Road Dept'],
            ['code'=>'electrical','name'=>'Electrical Dept'],
            ['code'=>'garbage','name'=>'Garbage Dept'],
            ['code'=>'streetlight','name'=>'Street Light Dept'],
            ['code'=>'water','name'=>'Water Dept'],
            ['code'=>'sanitation','name'=>'Sanitation Dept'],
            ['code'=>'others','name'=>'Others']
        ];
    }
    echo json_encode(['success' => true, 'departments' => $rows]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

