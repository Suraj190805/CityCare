<?php
// get_reports.php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getPDO();

    // Basic listing; you can add pagination / filters later
    $stmt = $pdo->query("SELECT id, uuid, type, problem, department, raw_department_value, location, image_filename, status, created_at FROM reports ORDER BY created_at DESC");
    $rows = $stmt->fetchAll();

    // Convert to friendly structure and include image_url if exists
    $data = array_map(function($r){
        $r['image_url'] = $r['image_filename'] ? ('/uploads/' . $r['image_filename']) : null;
        return $r;
    }, $rows);

    echo json_encode(['success' => true, 'reports' => $data]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
