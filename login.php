<?php
// login.php
require_once 'config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST allowed', 405);
    }

    $data = $_POST;
    if (empty($data)) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) $data = $json;
    }

    $identifier = trim($data['identifier'] ?? ''); // email or username
    $password = $data['password'] ?? '';

    if (!$identifier || !$password) {
        throw new Exception('Provide username/email and password', 400);
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE email = :id OR username = :id LIMIT 1");
    $stmt->execute([':id' => $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        throw new Exception('Invalid credentials', 401);
    }

    // authenticated — set session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    echo json_encode(['success' => true, 'message' => 'Logged in', 'user' => ['id'=>$user['id'], 'username'=>$user['username']]]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
