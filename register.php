<?php
// register.php
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST allowed', 405);
    }

    // accept JSON or form data
    $data = $_POST;
    if (empty($data)) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) $data = $json;
    }

    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$username || !$email || !$password) {
        throw new Exception('Please provide username, email and password', 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email', 400);
    }

    $pdo = getPDO();

    // check duplicate username/email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1");
    $stmt->execute([':email'=>$email, ':username'=>$username]);
    if ($stmt->fetch()) {
        throw new Exception('User with that email or username already exists', 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :hash)");
    $stmt->execute([':username'=>$username, ':email'=>$email, ':hash'=>$hash]);

    echo json_encode(['success' => true, 'message' => 'Account created']);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
