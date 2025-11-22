<?php
// manual_submit.php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST allowed', 405);
    }

    // read inputs (works with form POST or JSON body)
    $input = $_POST;
    if (empty($input)) {
        // maybe JSON body
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) $input = $json;
    }

    $problem = trim($input['problem'] ?? '');
    $department = trim($input['department'] ?? '');
    $raw_department_value = trim($input['raw_department_value'] ?? '');
    $location = trim($input['location'] ?? '');

    if ($problem === '') throw new Exception('Problem description required', 400);
    if ($department === '') throw new Exception('Department required', 400);
    if ($location === '') throw new Exception('Location required', 400);

    $pdo = getPDO();

    $uuid = 'rpt_' . time() . '_' . bin2hex(random_bytes(4));
    $stmt = $pdo->prepare("INSERT INTO reports (uuid, type, problem, department, raw_department_value, location, status, created_at) VALUES (:uuid, 'manual', :problem, :department, :raw_department_value, :location, 'Submitted', NOW())");
    $stmt->execute([
        ':uuid' => $uuid,
        ':problem' => $problem,
        ':department' => $department,
        ':raw_department_value' => $raw_department_value,
        ':location' => $location
    ]);

    $id = (int)$pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'report' => [
            'id' => $id,
            'uuid' => $uuid,
            'type' => 'manual',
            'problem' => $problem,
            'department' => $department,
            'location' => $location,
            'status' => 'Submitted',
            'created_at' => date('c')
        ]
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
