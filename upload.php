<?php
// upload.php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST allowed', 405);
    }

    if (!isset($_FILES['images'])) {
        throw new Exception('No files uploaded', 400);
    }

    $pdo = getPDO();

    // normalize files array
    $files = [];
    if (is_array($_FILES['images']['name'])) {
        // multiple
        foreach ($_FILES['images']['name'] as $i => $name) {
            $files[] = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i]
            ];
        }
    } else {
        $files[] = $_FILES['images'];
    }

    $savedFiles = [];
    foreach ($files as $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            continue; // skip error file (could also return error)
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            continue;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        global $allowed_mime;
        if (!in_array($mime, $allowed_mime)) {
            continue;
        }

        // generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)) . '_' . time();
        $filename = $basename . ($ext ? '.' . $ext : '');

        $destPath = UPLOAD_DIR . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            continue;
        }

        // create a DB row for each image (or you could group them into one report - here we create a report per image)
        $uuid = 'rpt_' . time() . '_' . bin2hex(random_bytes(4));
        $stmt = $pdo->prepare("INSERT INTO reports (uuid, type, image_filename, status, created_at) VALUES (:uuid, 'image', :image_filename, 'Submitted', NOW())");
        $stmt->execute([
            ':uuid' => $uuid,
            ':image_filename' => $filename
        ]);

        $reportId = $pdo->lastInsertId();
        $savedFiles[] = [
            'id' => (int)$reportId,
            'uuid' => $uuid,
            'image_filename' => $filename,
            'image_url' => '/uploads/' . $filename
        ];
    }

    if (empty($savedFiles)) {
        echo json_encode(['success' => false, 'message' => 'No files were accepted.']);
        exit;
    }

    echo json_encode(['success' => true, 'uploaded' => $savedFiles]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
